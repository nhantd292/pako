$("input[name='filter_keyword']").on('keydown', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        e.preventDefault();
        var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val(), filter_customer_type: customer_type_id, filter_warehouse: $(`select[name="inventory_id"]`).val()};
        load_action('#loadProducts', url_loadProduct, data);
    }
});

$(`select[name="filter_products_type"], select[name="inventory_id"]`).change(function () {
    var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val(), filter_customer_type: customer_type_id, filter_warehouse: $(`select[name="inventory_id"]`).val()};
    load_action('#loadProducts', url_loadProduct, data);
});

var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val(), filter_customer_type: customer_type_id, filter_warehouse: $(`select[name="inventory_id"]`).val()};
load_action('#loadProducts', '/xadmin/api/loadKovProducts/', data);

// Xử lý load tỉnh thành
$('select[name="location_city_id"]').change(function () {
    var select = 'input[name="location_district_id"]';
    var parent = $(select).parent();
    $('.select2-container', parent).select2('val', '');
    $(select).attr('data-parent', $(this).val());

    var select_x = 'input[name="location_town_id"]';
    var parent_x = $(select_x).parent();
    $('.select2-container', parent_x).select2('val', '');
    $(select_x).attr('data-parent', $(this).val());
});
$('input[name="location_district_id"]').change(function() {
    var select = 'input[name="location_town_id"]';
    var parent = $(select).parent();
    $('.select2-container', parent).select2('val', '');
    $(select).attr('data-parent', $(this).val());
});

var invoice_type_value = $('select[name="invoice_type"]').val()
if(invoice_type_value) {
    $('.' + invoice_type_value).removeClass('hidden')
}

$('select[name="invoice_type"]').change(function() {
    var invoice_type = $(this).val();
    $('.invoice_info').addClass('hidden')
    $('.'+invoice_type).removeClass('hidden')
});

var option_vat_value = $('select[name="option_vat"]').val()
if(option_vat_value) {
    $('.' + option_vat_value).removeClass('hidden')
}
$('select[name="option_vat"]').change(function() {
    var option_vat = $(this).val();
    $('.vat_infor').addClass('hidden')
    $('.'+option_vat).removeClass('hidden')
    $('select[name="invoice_type"]').select2('val', '');

    var invoice_type = $('select[name="invoice_type"]').val()
    $('.invoice_info').addClass('hidden')
    $('.'+invoice_type).removeClass('hidden')
});


// Kiểm tra thông tin khách hàng
if (contactId) {
    checkContactToElement(contactId, 'element');
}

$('input[name="paid_cash"] , input[name="paid_transfer"], input[name="discount"], input[name="fee_other"], input[name="fee_shipp"]').change(function() {
    updateTotal();
});

updateTotal(false)

function updateTotal() {
    var total_contract = 0;
    $.each($('.list-product-contract tr'), function (index, value) {
        var price = $(this).find('.price > input').val() ? $(this).find('.price > input').val() : 0;
        var number = $(this).find('.numbers input').val() ? $(this).find('.numbers input').val() : 0;
        total_contract += parseInt(unFormatNumber(price) * unFormatNumber(number));
    });
    var price_total = total_contract;

    var amount_owed = $("input[name=amount_owed]").val() ? parseInt(unFormatNumber($("input[name=amount_owed]").val())) : 0;
    var paid_cash = $("input[name=paid_cash]").val() ? parseInt(unFormatNumber($("input[name=paid_cash]").val())) : 0;
    var paid_transfer = $("input[name=paid_transfer]").val() ? parseInt(unFormatNumber($("input[name=paid_transfer]").val())) : 0;
    var discount = $("input[name=discount]").val() ? parseInt(unFormatNumber($("input[name=discount]").val())) : 0;
    var fee_other = $("input[name=fee_other]").val() ? parseInt(unFormatNumber($("input[name=fee_other]").val())) : 0;
    var fee_shipp = $("input[name=fee_shipp]").val() ? parseInt(unFormatNumber($("input[name=fee_shipp]").val())) : 0;


    var vat = Math.round(price_total - (price_total / (1+ percent_vat / 100)));
    $("input[name=vat]").val(formatNumber(vat))



    $("input[name=price_total]").val(formatNumber(price_total))
    $("input[name=new_debt]").val(formatNumber(amount_owed + (price_total + fee_shipp + fee_other) - (paid_cash + paid_transfer + discount)))
}
