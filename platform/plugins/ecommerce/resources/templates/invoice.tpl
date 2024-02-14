<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ 'plugins/ecommerce::order.invoice_for_order'|trans }} {{ invoice.code }}</title>

    {% if settings.using_custom_font_for_invoice and settings.custom_font_family %}
        <link href="https://fonts.googleapis.com/css?family={{ settings.custom_font_family }}:400,500,600,700,900&display=swap" rel="stylesheet">
    {% endif %}
    <style>
        body {
            font-size: 15px;
            font-family: '{{ settings.font_family }}', Arial, sans-serif !important;
        }

        table {
            border-collapse : collapse;
            width           : 100%
        }

        table tr td {
            padding : 0
        }

        table tr td:last-child {
            text-align : right
        }

        .bold, strong {
            font-weight : 700
        }

        .right {
            text-align : right
        }

        .large {
            font-size : 1.75em
        }

        .total {
            color       : #fb7578;
            font-weight : 700
        }

        .logo-container {
            margin : 20px 0 50px
        }

        .invoice-info-container {
            font-size : .875em
        }

        .invoice-info-container td {
            padding : 4px 0
        }

        .line-items-container {
            font-size : .875em;
            margin    : 70px 0
        }

        .line-items-container th {
            border-bottom  : 2px solid #ddd;
            color          : #999;
            font-size      : .75em;
            padding        : 10px 0 15px;
            text-align     : left;
            text-transform : uppercase
        }

        .line-items-container th:last-child {
            text-align : right
        }

        .line-items-container td {
            padding : 10px 0
        }

        .line-items-container tbody tr:first-child td {
            padding-top : 25px
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            border-bottom  : 2px solid #ddd;
            padding-bottom : 25px
        }

        .line-items-container th.heading-quantity {
            width : 50px
        }

        .line-items-container th.heading-price {
            text-align : right;
            width      : 100px
        }

        .line-items-container th.heading-subtotal {
            width : 100px
        }

        .payment-info {
            font-size   : .875em;
            line-height : 1.5;
            width       : 38%
        }

        small {
            font-size : 80%
        }

        .stamp {
            border         : 2px solid #555;
            color          : #555;
            display        : inline-block;
            font-size      : 18px;
            font-weight    : 700;
            left           : 30%;
            line-height    : 1;
            opacity        : .5;
            padding        : .3rem .75rem;
            position       : fixed;
            text-transform : uppercase;
            top            : 40%;
            transform      : rotate(-14deg)
        }

        .is-failed {
            border-color : #d23;
            color        : #d23
        }

        .is-completed {
            border-color : #0a9928;
            color        : #0a9928
        }
    </style>

    {{ invoice_header_filter | raw }}
</head>
<body>

{{ invoice_body_filter | raw }}

{% if (get_ecommerce_setting('enable_invoice_stamp', 1) == 1) %}
    {% if invoice.status == 'canceled' %}
        <span class="stamp is-failed">
            {{ invoice.status }}
        </span>
    {% else %}
        <span class="stamp {% if payment_status == 'completed' %} is-completed {% else %} is-failed {% endif %}">
            COMPLETATO
        </span>
    {% endif %}
{% endif %}

<table class="invoice-info-container">
    <tr>
        <td>
            <div class="logo-container">
                {% if logo %}
                    <img src="https://marigopharma.marigo.collaudo.biz/storage/logo-header-4.png" style="width:100%; max-width:150px;" alt="{{ 'site_title' }}">
                {% endif %}
            </div>
        </td>
        <td>
            {% if invoice.created_at %}
                <p>
                    <strong>{{ invoice.created_at|date('F d, Y') }}</strong>
                </p>
            {% endif %}
            <p>
                <strong>Ordine</strong>
                #100000{{ invoice.reference_id }}
            </p>
        </td>
    </tr>
</table>

<table class="invoice-info-container">
    <tr>
        <td>
            <p>Marigo Italia S.R.L.</p>

            <p>Via Bagnulo, 168 – Piano di Sorrento (NA)</p>


            <p>Partita IVA: 07500660639</p>
        </td>
        <td>
            {% if invoice.customer_name %}
                <p>{{ invoice.customer_name }}</p>
            {% endif %}
            {% if invoice.customer_email %}
                <p>{{ invoice.customer_email }}</p>
            {% endif %}
            {% if invoice.customer_address %}
                <p>{{ invoice.customer_address }}</p>
            {% endif %}
            {% if invoice.customer_phone %}
                <p>{{ invoice.customer_phone }}</p>
            {% endif %}
        </td>
    </tr>
</table>

{% if invoice.description %}
    <table class="invoice-info-container">
        <tr style="text-align: left">
            <td style="text-align: left">
                <p>{{ 'plugins/ecommerce::order.note'|trans }}: {{ invoice.description }}</p>
            </td>
        </tr>
    </table>
{% endif %}

<table class="line-items-container">
    <thead>
    <tr>
        <th class="heading-description">Prodotto (Codice)</th>
        <th class="heading-description">{{ 'plugins/ecommerce::products.form.options'|trans }}</th>
        <th class="heading-quantity">{{ 'plugins/ecommerce::products.form.quantity'|trans }}</th>
        <th class="heading-price">{{ 'plugins/ecommerce::products.form.price'|trans }}</th>
        <th class="heading-subtotal">{{ 'plugins/ecommerce::products.form.total'|trans }}</th>
    </tr>
    </thead>
    <tbody>
    {% for item in invoice.items %}
        <tr>
            <td>{{ item.name }} ({{item.sku}})</td>
            <td>{{ item.options }}</td>
            <td>{{ item.qty }}</td>
            <td class="right">{{ item.sub_total|price_format }}</td>
            <td class="bold">{{ item.amount|price_format }}</td>
        </tr>
    {% endfor %}

    {% if invoice.shipping_amount %}
        <tr>
            <td colspan="4" class="right">
                Contributo per spedizione ed imballaggio
            </td>
            <td class="bold">
                {{ invoice.shipping_amount|price_format }}
            </td>
        </tr>
    {% endif %}

    {% if is_tax_enabled %}
        <tr>
            <td colspan="4" class="right">
                IVA
            </td>
            <td class="bold">
                {{ invoice.tax_amount|price_format }}
            </td>
        </tr>
    {% endif %}
    
    {% if invoice.discount_amount %}
        <tr>
            <td colspan="4" class="right">
                {{ 'plugins/ecommerce::products.form.discount'|trans }}
            </td>
            <td class="bold">
                {{ invoice.discount_amount|price_format }}
            </td>
        </tr>
    {% endif %}
    </tbody>
</table>

<table class="line-items-container">
    <thead>
    <tr>
        <th>{{ 'plugins/ecommerce::order.payment_info'|trans }}</th>
        <th>Totale IVA Inclusa</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="payment-info">
            {% if payment_method %}
                <div>
                    {{ 'plugins/ecommerce::order.payment_method'|trans }}: <strong>{{ payment_method }}</strong>
                </div>
            {% endif %}

            {% if payment_status %}
                <div>
                    {{ 'plugins/ecommerce::order.payment_status_label'|trans }}: <strong>COMPLETATO</strong>
                </div>
            {% endif %}

            {% if payment_description %}
                <div>
                    {{ 'plugins/ecommerce::order.payment_info'|trans }}: <strong>{{ payment_description | raw }}</strong>
                </div>
            {% endif %}
        </td>
        <td class="large total">{{ invoice.amount|price_format }}</td>
    </tr>
    </tbody>
</table>
{{ ecommerce_invoice_footer | raw }}
</body>
</html>
