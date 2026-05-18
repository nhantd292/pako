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
