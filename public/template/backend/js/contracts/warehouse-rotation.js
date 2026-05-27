
var data = {filter_products_type: $(`select[name="filter_products_type"]`).val(), filter_keyword: $(`input[name="filter_keyword"]`).val(), filter_warehouse: $(`select[name="inventory_output_id"]`).val()};
load_action('#loadProducts', '/xadmin/api/loadProductsWarehouseRotation/', data);

