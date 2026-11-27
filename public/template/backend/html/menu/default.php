<?php
    $this->_userInfo = new \ZendX\System\UserInfo();
    $curent_user = $this->_userInfo->getUserInfo();
    $permission_ids = explode(',', $curent_user['permission_ids']);

    $is_system = true;
    $is_admin = true;
    $is_marketing = true;
    $is_sales = true;
    $is_product = true;
    $is_check_oder = true;
    $is_accounting = true;

    $is_system = false;
    if(in_array('system', $permission_ids)){
        $is_system = true;
    }
?>
<ul class="nav navbar-nav">

    <!--Nhân sự-->
	<li class="parent">
		<a href="javascript:;" target="_self" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" class="dropdown-toggle">
			<i class="fa fa-users"></i> <span class="title">Nhân sự</span><span class="arrow"></span>
		</a>
		<ul class="dropdown-menu">
            <li class="dropdown-submenu">
                <a href="javascript:;" target="_self">
                    <i class="fa fa-folder-o"></i> <span class="title">Sơ đồ tổ chức</span><span class="arrow"></span>
                </a>
                <ul class="dropdown-menu" style="display: none;">
                    <li>
                        <a href="<?php echo $this->url('routeAdminDocument/default', array('slug' => 'sale-branch', 'action' => 'index'));?>" target="_self">
                            <i class="fa fa-sitemap"></i> <span class="title">Cơ sở</span><span class="arrow"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->url('routeAdminDocument/default', array('slug' => 'company-department', 'action' => 'index'));?>" target="_self">
                            <i class="fa fa-sitemap"></i> <span class="title">Phòng ban</span><span class="arrow"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->url('routeAdminDocument/default', array('slug' => 'lists-group', 'action' => 'index'));?>" target="_self">
                            <i class="fa fa-sitemap"></i> <span class="title">Đội nhóm</span><span class="arrow"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->url('routeAdminDocument/default', array('slug' => 'company-position', 'action' => 'index'));?>" target="_self">
                            <i class="fa fa-sitemap"></i> <span class="title">Vị trí/Chức vụ</span><span class="arrow"></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
				<a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'user', 'action' => 'index'));?>" target="_self">
					<i class="fa fa-user"></i> <span class="title">Nhân viên</span><span class="arrow"></span>
				</a>
			</li>

            <li class="divider"></li>

            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'dynamic', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-folder-o"></i> <span class="title">Cấu hình chuyên mục</span><span class="arrow"></span>
                </a>
            </li>

            <?php if($curent_user['id'] = '1111111111111111111111'){?>
            <li>
                <a href="<?php echo $this->url('routeAdminNested/default', array('controller' => 'setting', 'action' => 'index', 'code' => 'System'));?>" target="_self">
                    <i class="fa fa-cog"></i> <span class="title">Cấu hình hệ thống</span><span class="arrow"></span>
                </a>
            </li>
            <?php }?>
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'permission', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-users"></i> <span class="title">Nhóm quyền truy cập</span><span class="arrow"></span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'permission-list', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-universal-access"></i> <span class="title">Danh sách quyền truy cập</span><span class="arrow"></span>
                </a>
            </li>
        </ul>
	</li>

    <!--Khách hàng-->
    <li class="parent">
        <a href="javascript:;" target="_self" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" class="dropdown-toggle">
            <i class="fa fa-user"></i> <span class="title">Khách hàng</span><span class="arrow"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'contact', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-list-ul"></i> <span class="title">Danh sách Khách hàng</span><span class="arrow"></span>
                </a>
            </li>
        </ul>
    </li>

    <!--Bán hàng-->
    <li class="parent">
        <a href="javascript:;" target="_self" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" class="dropdown-toggle">
            <i class="fa fa-money"></i> <span class="title">Bán hàng</span><span class="arrow"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'contract', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-book"></i> <span class="title">Đơn hàng</span><span class="arrow"></span>
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'orders-return', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-reply-all"></i> <span class="title">Phiếu KH trả hàng</span><span class="arrow"></span>
                </a>
            </li>
        </ul>
    </li>

    <!--Tài chính-->
    <li class="parent">
        <a href="<?php echo $this->url('routeHome');?>" target="_self" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" class="dropdown-toggle">
            <i class="fa fa-archive"></i> <span class="title">Tài chính</span><span class="arrow"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?php echo $this->url('routeAdmin/default', array('controller' => 'accountant-bill', 'action' => 'index'));?>" target="_self">
                    <i class="fa fa-undo"></i> <span class="title">Quản lý thu-chi</span><span class="arrow"></span>
                </a>
            </li>
        </ul>
    </li>

</ul>