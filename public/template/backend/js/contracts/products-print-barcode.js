
var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val()};
load_action('#loadProducts', '/xadmin/api/loadProductsVat/', data);

