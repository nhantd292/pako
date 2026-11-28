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