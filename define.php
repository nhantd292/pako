<?php
// === Load .env ===
require_once 'config/load_env.php';
loadEnv(__DIR__ . '/.env'); // nạp file .env ở gốc project
// =================

// Đường dẫn đến thư mục chứa thư mục hiện thời
chdir(dirname(__FILE__));

// Key App

define('CLIENT_ID',     getenv('CLIENT_ID'));
define('CLIENT_SECRET', getenv('CLIENT_SECRET'));
define('RETAILER',      getenv('RETAILER'));

define('GHTK',      '6A538Aa24d481851A22D3ACD425c9FB503D5Fa81');//nhantd
//define('GHTK',      '0f1d03F9b1c5fA3Cd6732633C5b0d018E3916dd4');// anh oanh
//define('URLGHTK',   'https://services.ghtklab.com');
define('URLGHTK',   'https://services.giaohangtietkiem.vn');
define('HASS',      '6A538A1ASD4FG8D83D5FG8D5D4FAS8E8DF4D5D54F4');
define('UPDATESHIPMENT',      'http://crm.5sauto.local/xadmin/api/updateShipment?hash=6A538A1ASD4FG8D83D5FG8D5D4FAS8E8DF4D5D54F4');

//define('VIETTELPOST_CODE_SECRET', '480F1AA0CBD582AFB310B397C7950E0C');
//define('VIETTELPOST_CODE_SECRET', '9CD92FFBD93C73CF098E5E0017AE2C87');
define('TOKENUPDATE', 'DFG8DFGJDFG098DJGDAO89FUG90ASDFGUDSFG908SDJFG90SD8FUGJASDFG8MSDH');

define('URLWEBHOOK','https://webhook.site/f941080a-4646-47d4-adca-18817f664cf8');


define('URL_GHN',   'https://dev-online-gateway.ghn.vn/shiip/public-api/v2');
define('URL_GHN_PRINT',   'https://dev-online-gateway.ghn.vn/a5/public-api/print80x80');

define('BANK_TECHCOMBANK',      '1706');

// Key App
define('APP_KEY',                   'x2019');

// Định nghĩa môi trường thực thi của ứng dụng
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));	// development - production

// Định nghĩa đường dẫn đến thư mục ứng dụng
define('PATH_APPLICATION',          realpath(dirname(__FILE__)));
define('PATH_LIBRARY',              PATH_APPLICATION . '/library');
define('PATH_VENDOR',               PATH_APPLICATION . '/vendor');
define('PATH_PUBLIC',               PATH_APPLICATION . '/public');
define('PATH_CAPTCHA',              PATH_PUBLIC . '/captcha');
define('PATH_FILES',                PATH_PUBLIC . '/files');
define('PATH_SCRIPTS',              PATH_PUBLIC . '/scripts');
define('PATH_TEMPLATE',             PATH_PUBLIC . '/template');

// Định nghĩa đường dẫn url
define('URL_APPLICATION',           '');
define('URL_MODULE',                URL_APPLICATION . '/module');
define('URL_PUBLIC',                URL_APPLICATION . '/public');
define('URL_FILES',                 URL_PUBLIC . '/files');
define('URL_SCRIPTS',               URL_PUBLIC . '/scripts');
define('URL_TEMPLATE',              URL_PUBLIC . '/template');

// HTMLPurifier
define('HTMLPURIFIER_PREFIX',       PATH_VENDOR);

// Table HR
define('TABLE_PREFIX',              'x_');
define('TABLE_USER',                TABLE_PREFIX . 'user');
define('TABLE_PERMISSION',     		TABLE_PREFIX . 'permission');
define('TABLE_PERMISSION_LIST',		TABLE_PREFIX . 'permission_list');

define('TABLE_DYNAMIC',             TABLE_PREFIX . 'dynamic');
define('TABLE_DOCUMENT',            TABLE_PREFIX . 'document');
define('TABLE_LOCATIONS',           TABLE_PREFIX . 'locations');

define('TABLE_SETTING',             TABLE_PREFIX . 'setting');
define('TABLE_CONTACT',             TABLE_PREFIX . 'contact');
define('TABLE_CONTRACT',            TABLE_PREFIX . 'contract');
define('TABLE_CONTRACT_DETAIL',     TABLE_PREFIX . 'contract_detail');
define('TABLE_BILL',                TABLE_PREFIX . 'bill');
define('TABLE_PRODUCT',             TABLE_PREFIX . 'product');
define('TABLE_PRODUCT_SETTING',     TABLE_PREFIX . 'product_setting');
define('TABLE_MATTER',              TABLE_PREFIX . 'matter');
define('TABLE_SALES_TARGET',        TABLE_PREFIX . 'sales_target');
define('TABLE_MARKETING_REPORT',    TABLE_PREFIX . 'marketing_report'); // Báo cáo marketing
define('TABLE_MARKETING_ADS',       TABLE_PREFIX . 'marketing_ads'); // Báo cáo ads
define('TABLE_MATERIAL',            TABLE_PREFIX . 'material'); // Bảng nguyên liệu đầu kỳ
define('TABLE_EVENT',               TABLE_PREFIX . 'event');
define('TABLE_EVENT_CONTACT',		TABLE_PREFIX . 'event_contact');
define('TABLE_HISTORY',             TABLE_PREFIX . 'history');
define('TABLE_LOGS',                TABLE_PREFIX . 'logs');
define('TABLE_HISTORY_IMPORT',      TABLE_PREFIX . 'history_import');
define('TABLE_CAMPAIGN',            TABLE_PREFIX . 'campaign');
define('TABLE_CAMPAIGN_DATA',       TABLE_PREFIX . 'campaign_data');
define('TABLE_FORM_DATA',           TABLE_PREFIX . 'form_data');
define('TABLE_PRODUCT_LISTED',      TABLE_PREFIX . 'product_listed');
define('TABLE_COLOR',               TABLE_PREFIX . 'color');
define('TABLE_LINK_CHECKING',       TABLE_PREFIX . 'link_checking'); // Link tracking
define('TABLE_DATA_CONFIG',         TABLE_PREFIX . 'data_config'); // Cấu hình chi data tự động
define('TABLE_CHECK_IN',            TABLE_PREFIX . 'check_in'); // Chấm công
define('TABLE_TARGET',              TABLE_PREFIX . 'target'); // Bảng chỉ tiêu
define('TABLE_EVALUATE',            TABLE_PREFIX . 'evaluate'); // Bảng đánh giá nhân sự
define('TABLE_NOTIFI',              TABLE_PREFIX . 'notifi'); // Bảng thông báo
define('TABLE_NOTIFI_USER',         TABLE_PREFIX . 'notifi_user'); // Bảng trạng thái thông báo
define('TABLE_COMBO_PRODUCT',       TABLE_PREFIX . 'combo_product'); // Bảng combo sản phẩm

// Bảng Kiotviet
define('TABLE_KOV_BRANCHES',        TABLE_PREFIX . 'kov_branches'); // Kho hàng
define('TABLE_KOV_PRODUCTS',        TABLE_PREFIX . 'kov_products'); // Sản phẩm
define('TABLE_KOV_DISCOUNTS',       TABLE_PREFIX . 'kov_discounts'); // Quản lý khuyến mãi
define('TABLE_KOV_PRODUCT_BRANCH',  TABLE_PREFIX . 'kov_product_branch'); // Số lượng giá vốn sản phẩm
define('TABLE_PRODUCT_RETURN',      TABLE_PREFIX . 'product_return'); // Quản lý kho hàng hoàn
define('TABLE_PRODUCT_RETURN_KOV',  TABLE_PREFIX . 'product_return_kov'); // Quản lý kho hàng hoàn về kov
define('TABLE_KOV_ORDERS',          TABLE_PREFIX . 'kov_orders'); // Đơn hàng
define('TABLE_KOV_INVOICES',        TABLE_PREFIX . 'kov_invoices'); // Hóa đơn

// Đào tạo
define('TABLE_EDU_CLASS',           TABLE_PREFIX . 'edu_class');

// Công việc
define('TABLE_TASK_CATEGORY',       TABLE_PREFIX . 'task_category');
define('TABLE_TASK_PROJECT',        TABLE_PREFIX . 'task_project');
define('TABLE_TASK_PROJECT_CONTENT',TABLE_PREFIX . 'task_project_content');
define('TABLE_TASK',                TABLE_PREFIX . 'task');

// Hội đồng Anh
define('TABLE_BC',                  TABLE_PREFIX . 'bc');
define('TABLE_BC_BILL',             TABLE_PREFIX . 'bc_bill');

// Notifycation
define('TABLE_NOTIFY',        		TABLE_PREFIX . 'notify');

// Zalo
define('TABLE_ZALO_NOTIFY_CONFIG',        		TABLE_PREFIX . 'zalo_notify_config');
define('TABLE_ZALO_NOTIFY_RESULT',        		TABLE_PREFIX . 'zalo_notify_result');

// Permisions
define('SYSTEM', 'system');
define('ADMIN', 'admin');
define('MANAGER', 'manager');
define('GDCN', 'ch'); // Giám đốc chi nhánh
define('ACCOUNTING', 'accounting'); // Giám đốc chi nhánh
define('SALEADMIN', 'saleadmin'); // Sale admim
define('SALES', 'sales'); // Nhân viên sale
define('MARKETINGADMIN', 'admin_marketing'); // Mkt admin
define('MARKETING', 'marketing'); // Nhân viên Mkt
define('GROUP_MKT_LEADER', 'group-marketing-leader'); // Trưởng nhóm MKT
define('GROUP_SALES_LEADER', 'group-sales-leader'); // Trưởng nhóm Sales
define('CHECK_MANAGER', 'check_manager'); // Quản lý giục đơn

// status code
define('STATUS_CONTACT_CANCEL'      , 'huy');
define('STATUS_CONTACT_UNHEARD'     , 'khong-nghe-may');
define('STATUS_CONTACT_THINK'       , 'suy-nghi');
define('STATUS_CONTACT_ADVISORY'    , 'da-tu-van');
define('STATUS_CONTACT_POSITIVE'    , 'tich-cuc');
define('STATUS_CONTACT_NEGATIVE'    , 'tieu-cuc');

// Phân loại lịch sử chăm sóc
define('DA_CHOT'    , 'da-chot');
define('HUY_SALES'  , 'huy-sales');
define('DANG_DONG_GOI'  , 'dang-dong-goi');
define('HUY'        , 'huy');

// Trạng thái sản xuất
define('STATUS_CONTRACT_PRODUCT_CANCEL'     , 'huy-san-xuat');
define('STATUS_CONTRACT_PRODUCT_PRODUCTED'  , 'da-san-xuat');
define('STATUS_CONTRACT_PRODUCT_POST'       , 'da-giao-hang');
define('STATUS_CONTRACT_PRODUCT_NOT_SEND'   , 'huy-khong-gui');

// Trạng thái kế toán
define('STATUS_CONTRACT_ACOUNTING_CANCEL'       , 'huy-khong-gui');
define('STATUS_CONTRACT_ACOUNTING_RETURN'       , 'da-nhan-hoan');
define('STATUS_CONTRACT_ACOUNTING_MONEY'        , 'da-nhan-tien');
define('STATUS_CONTRACT_ACOUNTING_CANCEL_RETURN', 'huy-hang-co-san');

// Trạng thái giục đơn
define('STATUS_CONTRACT_CHECK_KEEP'    , 'giu-lai-buu-dien');
define('STATUS_CONTRACT_CHECK_SENDING' , 'dang-van-chuyen');
define('STATUS_CONTRACT_CHECK_POST'    , 'dang-phat');
define('STATUS_CONTRACT_CHECK_SUCCESS' , 'thanh-cong');
define('STATUS_CONTRACT_CHECK_RETURN'  , 'hoan');

// Phân loại khách hàng
define('CONTACT_TYPE_ONE'      , 'khach-le');
define('CONTACT_TYPE_MULTIL'   , 'khach-dai-ly');

// Color
define('CARPET_COLOR', 'carpet-color');
define('TANGLED_COLOR', 'tangled-color');

// Loại đơn sản xuất
define('DON_BAO_HANH', 'don-bao-hanh');
define('DON_HA_NOI', 'don-ha-noi');
define('DON_TINH', 'don-tinh');

// Chức vụ kiêm nhiệm
define('NHAN_VIEN_GIAO_HANG', 'nhan-vien-giao-hang');

// Zalo
define('ZALO_NOTIFY_CONFIG_DATHANG', 'dat-hang');
define('ZALO_NOTIFY_CONFIG_XUATKHO', 'xuat-kho');
define('ZALO_NOTIFY_CONFIG_GIAOHANG', 'giao-hang');
define('ZALO_NOTIFY_CONFIG_THANHTOAN', 'thanh-toan');
define('ZALO_NOTIFY_CONFIG_CHAMSOC', 'cham-soc');



