$('input[name="paid_cash"] , input[name="paid_transfer"]').change(function() {
    updateTotal();
});
updateTotal()
function updateTotal() {
    var amount_owed = $("input[name=amount_owed]").val() ? parseInt(unFormatNumber($("input[name=amount_owed]").val())) : 0;
    var paid_cash = $("input[name=paid_cash]").val() ? parseInt(unFormatNumber($("input[name=paid_cash]").val())) : 0;
    var paid_transfer = $("input[name=paid_transfer]").val() ? parseInt(unFormatNumber($("input[name=paid_transfer]").val())) : 0;

    $("input[name=new_debt]").val(formatNumber(amount_owed - (paid_cash + paid_transfer)))
}

$(`input[name="customer_id"], select[name="inventory_id"]`).change(function () {
    var data = {filter_customer_id: $(`input[name="customer_id"]`).val(), filter_inventory_id: $(`select[name="inventory_id"]`).val()};
    load_action('#loadContracts', url_loadContracts, data);
});
var data = {filter_customer_id: $(`input[name="customer_id"]`).val(), filter_inventory_id: $(`select[name="inventory_id"]`).val()};
load_action('#loadContracts', url_loadContracts, data);
