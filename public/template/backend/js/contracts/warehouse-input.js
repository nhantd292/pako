
var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val(), filter_warehouse: $(`select[name="inventory_id"]`).val()};
load_action('#loadProducts', '/xadmin/api/loadProductsWarehouseInput/', data);

$('input[name="paid_cash"] , input[name="paid_transfer"], input[name="discount"], input[name="amount_owed"]').change(function() {
    updateTotal();
});

updateTotal()

function updateTotal() {
    var total_contract = 0;
    var total_contract_product = 0;
    var total_contract_vat = $('.total_contract_vat input').val() ? $('.total_contract_vat input').val() : 0;
    var fee_other = $('.fee_other input').val() ? $('.fee_other input').val() : 0;
    var total_contract_discount = $('.total_contract_discount input').val() ? $('.total_contract_discount input').val() : 0;
    var price_deposits = $('.price_deposits input').val() ? $('.price_deposits input').val() : 0;
    var amount_owed = $("input[name=amount_owed]").val() ? parseInt(unFormatNumber($("input[name=amount_owed]").val())) : 0;
    var paid_cash = $("input[name=paid_cash]").val() ? parseInt(unFormatNumber($("input[name=paid_cash]").val())) : 0;
    var paid_transfer = $("input[name=paid_transfer]").val() ? parseInt(unFormatNumber($("input[name=paid_transfer]").val())) : 0;
    var discount = $("input[name=discount]").val() ? parseInt(unFormatNumber($("input[name=discount]").val())) : 0;

    $.each($('.list-product-contract tr'), function (index, value) {
        var price = $(this).find('.price > input').val() ? $(this).find('.price > input').val() : 0;
        var number = $(this).find('.numbers input').val() ? $(this).find('.numbers input').val() : 0;
        total_contract_product += parseInt(unFormatNumber(price) * unFormatNumber(number));
        total_contract += parseInt(unFormatNumber(price) * unFormatNumber(number));
    });
    total_contract += parseInt(unFormatNumber(total_contract_vat));
    total_contract += parseInt(unFormatNumber(fee_other));
    total_contract -= parseInt(unFormatNumber(total_contract_discount));
    var price_total = total_contract;
    total_contract -= parseInt(unFormatNumber(price_deposits));
    $(".total_contract_vat input").val(formatNumber(total_contract_vat));
    $(".price_owed span").text(formatNumber(total_contract));
    $(".price_owed input").val(formatNumber(total_contract));
    $(".total_contract_product span").text(formatNumber(total_contract_product));
    $(".total_contract_product input").val(formatNumber(total_contract_product));

    $("input[name=price_total]").val(formatNumber(price_total))
    $("input[name=new_debt]").val(formatNumber(amount_owed - price_total + (paid_cash + paid_transfer + discount)))
}

function resetDiscounts() {
    $('.total_contract_discount input').val(0)
    $('tr.product_gif').remove()
}