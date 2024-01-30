<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Http\Requests\CartRequest;
use Botble\Ecommerce\Http\Requests\UpdateCartRequest;
use Botble\Ecommerce\Repositories\Eloquent\OrderRepository;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Http\Controllers\SaveCartController;
use Cart;
use EcommerceHelper;
use Exception;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Auth\Events\Login;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use OrderHelper;
use SeoHelper;
use Illuminate\Support\Facades\DB;
use Theme;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariants;

class PublicCartController extends Controller
{
    protected ProductInterface $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function store(CartRequest $request, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        $product = $this->productRepository->findById($request->input('id'));
        dd($product->is_variation);
        if(auth('customer')->user()!==NULL){
            $userid=auth('customer')->user()->id;
            $pricelist=DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product->id and customer_id=$userid");
            if(isset($pricelist[0]))
            {
            $reserved_price = $pricelist[0]->final_price;
            $offerDetail = OffersDetail::where('product_id', $product->id)
                ->where('customer_id', $userid)
                ->where('status', 'active')
                ->first();
            if ($offerDetail) {
                $offer = Offers::find($offerDetail->offer_id);
                if ($offer) {
                    $offerType = $offer->offer_type;
                }
            }
                if($offerDetail){

                }
                $product_price=$pricelist[0]->final_price;
            }else{
                $product_price=$product->price;
            }
        }else{
            $product_price=$product->price;
        }

        if (! $product) {
            return $response
                ->setError()
                ->setMessage(__('This product is out of stock or not exists!'));
        }

        if ($product->variations->count() > 0 && ! $product->is_variation) {
            $product = $product->defaultVariation->product;
        }

        if ($product->isOutOfStock()) {
            return $response
                ->setError()
                ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name ?: $product->name]));
        }

        $maxQuantity = $product->quantity;

        if (! $product->canAddToCart($request->input('qty', 1))) {
            return $response
                ->setError()
                ->setMessage(__('Maximum quantity is :max!', ['max' => $maxQuantity]));
        }

        $product->quantity -= $request->input('qty', 1);

        $outOfQuantity = false;
        foreach (Cart::instance('cart')->content() as $item) {
            if ($item->id == $product->id) {
                $originalQuantity = $product->quantity;
                $product->quantity = (int)$product->quantity - $item->qty;

                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }

                if ($product->isOutOfStock()) {
                    $outOfQuantity = true;

                    break;
                }

                $product->quantity = $originalQuantity;
            }
        }

        if ($product->original_product->options()->where('required', true)->exists()) {
            if (! $request->input('options')) {
                return $response
                    ->setError()
                    ->setData(['next_url' => $product->original_product->url])
                    ->setMessage(__('Please select product options!'));
            }

            $requiredOptions = $product->original_product->options()->where('required', true)->get();

            $message = null;

            foreach ($requiredOptions as $requiredOption) {
                if (! $request->input('options.' . $requiredOption->id . '.values')) {
                    $message .= trans('plugins/ecommerce::product-option.add_to_cart_value_required', ['value' => $requiredOption->name]);
                }
            }

            if ($message) {
                return $response
                    ->setError()
                    ->setMessage(__('Please select product options!'));
            }
        }

        if ($outOfQuantity) {
            return $response
                ->setError()
                ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name ?: $product->name]));
        }
        $product->price=$product_price;
        if($request->input('final_price')){
            $product->sale_price=$request->input('final_price');
        }
        $cartItems = OrderHelper::handleAddCart($product, $request);
        $response
            ->setMessage(__(
                'Added product :product to cart successfully!',
                ['product' => $product->original_product->name ?: $product->name]
            ));

        $token = OrderHelper::getOrderSessionToken();

        $nextUrl = route('public.checkout.information', $token);

        if (EcommerceHelper::getQuickBuyButtonTarget() == 'cart') {
            $nextUrl = route('public.cart');
        }

        if ($request->input('checkout')) {
            $response->setData(['next_url' => $nextUrl]);

            if ($request->ajax() && $request->wantsJson()) {
                return $response;
            }

            return $response
                ->setNextUrl($nextUrl);
        }

        if(auth('customer')->user()!==null){

            SaveCartController::saveCart(session('cart'));

        }
        return $response
            ->setData([
                'status' => true,
                'count' => Cart::instance('cart')->count(),
                'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => $cartItems,
            ]);
    }

    public function getView(HandleApplyPromotionsService $applyPromotionsService)
    {
        if(request()->user('customer')){
            if (! EcommerceHelper::isCartEnabled()) {
                abort(404);
            }

            Theme::asset()
                ->container('footer')
                ->add('ecommerce-checkout-js', 'vendor/core/plugins/ecommerce/js/checkout.js', ['jquery']);

            $promotionDiscountAmount = 0;
            $couponDiscountAmount = 0;

            $products = [];
            $crossSellProducts = collect();

            if (Cart::instance('cart')->count() > 0) {
                $products = Cart::instance('cart')->products();

                $promotionDiscountAmount = $applyPromotionsService->execute();

                $sessionData = OrderHelper::getOrderSessionData();


                if (session()->has('applied_coupon_code')) {
                    $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount', 0);
                }


                $parentIds = $products->pluck('original_product.id')->toArray();

                $crossSellProducts = get_cart_cross_sale_products($parentIds, theme_option('number_of_cross_sale_product', 4));
            }

            SeoHelper::setTitle(__('Shopping Cart'));

            Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Shopping Cart'), route('public.cart'));

            $order=null;
            if (Session::get('cart_order')){
                $order = resolve(OrderInterface::class)->findOrFail(Session::get('cart_order'));
            }

            return Theme::scope(
                'ecommerce.cart',
                compact('promotionDiscountAmount','order', 'couponDiscountAmount', 'products', 'crossSellProducts'),
                'plugins/ecommerce::themes.cart'
            )->render();
        }else{
            return Theme::scope('ecommerce.customers.login', [], 'plugins/ecommerce::themes.customers.login')->render();
        }

    }

    public function postUpdate(UpdateCartRequest $request, BaseHttpResponse $response)
    {

        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }
        Session::put('note',$request->note);
        Session::put('shippingAmount',$request->shippingAmount);
        if ($request->has('checkout')) {
            $token = OrderHelper::getOrderSessionToken();
            return $response->setNextUrl(route('public.checkout.information', $token));
        }

        $data = $request->input('items', []);

        $outOfQuantity = false;
        foreach ($data as $item) {
            $cartItem = Cart::instance('cart')->get($item['rowId']);

            if (! $cartItem) {
                continue;
            }

            $product = null;

            $product = $this->productRepository->findById($cartItem->id);

            if ($product) {
                $originalQuantity = $product->quantity;
                $product->quantity = (int)$product->quantity - (int)Arr::get($item, 'values.qty', 0) + 1;

                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }

                if ($product->isOutOfStock()) {
                    $outOfQuantity = true;
                } else {
                    Cart::instance('cart')->update($item['rowId'], Arr::get($item, 'values'));
                }

                $product->quantity = $originalQuantity;
            }
        }

        if ($outOfQuantity) {
            return $response
                ->setError()
                ->setData([
                    'count' => Cart::instance('cart')->count(),
                    'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                    'content' => Cart::instance('cart')->content(),
                ])
                ->setMessage(__('One or all products are not enough quantity so cannot update!'));
        }

        if(auth('customer')->user()!==null){

            SaveCartController::saveCart(session('cart'));

        }

        return $response
            ->setData([
                'count' => Cart::instance('cart')->count(),
                'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => Cart::instance('cart')->content(),
            ])
            ->setMessage(__('Update cart successfully!'));
    }

    public function getRemove(string $id, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        try {
            if (Cart::instance('cart')->count() == 1 && Session::get('cart_order')){
                Session::forget('cart_order');
            }
            Cart::instance('cart')->remove($id);
        } catch (Exception) {
            return $response->setError()->setMessage(__('Cart item is not existed!'));
        }

        if(auth('customer')->user()!==null){

            SaveCartController::saveCart(session('cart'));

        }

        return $response
            ->setData([
                'count' => Cart::instance('cart')->count(),
                'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => Cart::instance('cart')->content(),
            ])
            ->setMessage(__('Removed item from cart successfully!'));
    }

    public function getDestroy(BaseHttpResponse $response)
    {
        if (! EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        Cart::instance('cart')->destroy();

        return $response
            ->setData(Cart::instance('cart')->content())
            ->setMessage(__('Empty cart successfully!'));
    }
}
