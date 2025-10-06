
<?php
include_once('../configuration.php');
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_type_id']) && isset($_SESSION['user_name'])) {
    $user_id = $_SESSION['user_id'];
    $user_type_id = $_SESSION['user_type_id'];
    $role_type_name = $_SESSION['role_type_name'];
    $user_name = $_SESSION['user_name'];
    $user_img = $_SESSION['user_img'];
    $user_mob = $_SESSION['user_mob'];
    $user_add = $_SESSION['user_add'];
    $comp_id = $_SESSION['comp_id'];
    $branch_id = $_SESSION['branch_id'];
    $dept_id = $_SESSION['dept_id'];
    $user_img = $_SESSION['user_img'];
    $financial_year = $_SESSION['financial_year'];

	$rstcomp = mysqli_query($connect,"select comp_name from company_master where comp_id='$comp_id' ");
	$rwcomp = mysqli_fetch_assoc($rstcomp);
} else {
    echo "<script>window.location.href='../logout.php'</script>";
}
$rstuser = mysqli_query($connect,"select status from user_master where user_id= $user_id");
$rwuser = mysqli_fetch_assoc($rstuser);
if($rwuser['status']=="Deactive")
{
    echo "<script>window.location.href='../logout.php'</script>";
}
$current_url = basename($_SERVER['REQUEST_URI']);
$todaydate = date('Y-m-d');

// purchase order notification
$rstporequest = mysqli_query($connect,"select p.po_custom_number,p.po_date,p.total_amt,cm.client_name,cc.currency_code from 
purchase_order p inner join client_master cm on cm.client_id=p.supplier_id 
left join country_master cc on cc.id= cm.office_country_id 
where p.purchase_order_status='-1' and p.active_status=1 order by p.po_id desc ");
$porequestcount = mysqli_num_rows($rstporequest);

$rstpoapprovalDetails = mysqli_query($connect,"select p.po_custom_number,p.po_date,p.total_amt,cm.client_name,
p.purchase_order_status,cc.currency_code from purchase_order p inner join client_master cm on cm.client_id=p.supplier_id 
left join country_master cc on cc.id= cm.office_country_id 
where p.purchase_order_status IN (1,2) and p.active_status=1 and p.user_id='$user_id' and p.po_date='$todaydate' order by p.po_id desc ");
$poapprovaldetails = mysqli_num_rows($rstpoapprovalDetails);

// pro forma notification
$rstProapprovaldetails = mysqli_query($connect,"select p.pi_custom_number,p.pi_invoice_date,p.grand_total,
cm.client_name,p.pro_forma_status,cc.currency_code from pro_forma_head p inner join client_master cm on 
cm.client_id=p.account_id inner join country_master cc on cc.id= p.currency_id   where p.pro_forma_status 
IN (1,2) and p.active_status=1 and p.user_id='$user_id' 
and p.pi_invoice_date='$todaydate' order by p.pi_no desc ");
$Proapprovaldetails = mysqli_num_rows($rstProapprovaldetails);

$rstProrequest = mysqli_query($connect,"select p.pi_custom_number,p.pi_invoice_date,p.grand_total,
cm.client_name,cc.currency_code from pro_forma_head p inner join client_master cm on cm.client_id=p.account_id 
inner join country_master cc on cc.id= p.currency_id where 
p.pro_forma_status='0' and p.active_status=1 order by p.pi_no desc");
$prorequestcount = mysqli_num_rows($rstProrequest);

//purchase payment notification
$rstPOPaymentRequest = mysqli_query($connect,"select pp.paid_amount,ph.po_custom_number,pp.paid_date,
cc.currency_code from purchase_order_receipt_payment pp inner join purchase_order ph on ph.po_id=pp.po_no 
inner join country_master cc on cc.id= ph.currency_id where pp.approval_status = -1 and ph.active_status=1 order by pp.customer_receipt_id desc");
$POPaymentRequestCount = mysqli_num_rows($rstPOPaymentRequest);

$rstPOPaymentApprovedDetails = mysqli_query($connect,"select pp.paid_amount,ph.po_custom_number,pp.paid_date, 
cc.currency_code,pp.approval_status from purchase_order_receipt_payment pp inner join purchase_order ph on ph.po_id=pp.po_no 
inner join country_master cc on cc.id= ph.currency_id where pp.approval_status IN (1, 0) and pp.payment_status=0 
and ph.active_status=1 and pp.paid_date='$todaydate' order by pp.customer_receipt_id desc");
$POPaymentApprovedDetailsCount = mysqli_num_rows($rstPOPaymentApprovedDetails);

//purchase payment transaction notification
$rstPOPaymentTTRequest = mysqli_query($connect,"select pp.paid_amount,ph.po_custom_number,pp.paid_date,
cc.currency_code,pp.TransactionProof from purchase_order_receipt_payment pp inner join purchase_order ph on ph.po_id=pp.po_no 
inner join country_master cc on cc.id= ph.currency_id where pp.approval_status = 1 and pp.payment_status = -1 and ph.active_status=1 order by pp.customer_receipt_id desc");
$POPaymentTTRequestCount = mysqli_num_rows($rstPOPaymentTTRequest);

$rstPOPaymentTTApprovedDetails = mysqli_query($connect,"select pp.paid_amount,ph.po_custom_number,pp.paid_date, 
cc.currency_code,pp.payment_status,pp.TransactionProof from purchase_order_receipt_payment pp inner join purchase_order ph on ph.po_id=pp.po_no 
inner join country_master cc on cc.id= ph.currency_id where pp.approval_status=1 and pp.payment_status IN (1, -2) 
and ph.active_status=1 and pp.paid_date='$todaydate' order by pp.customer_receipt_id desc");
$POPaymentTTApprovedDetailsCount = mysqli_num_rows($rstPOPaymentTTApprovedDetails);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>ELTEE DMCC</title>

	<!-- Bootstrap -->
	<link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- NProgress -->
	<link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
	<!-- iCheck -->
	<link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	<!-- bootstrap-wysiwyg -->
	<!-- <link href="../vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet"> -->
	<!-- Select2 -->
	<!-- <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet"> -->
	<!-- Switchery -->
	<link href="../vendors/switchery/dist/switchery.min.css" rel="stylesheet">
	<!-- starrr -->
	<link href="../vendors/starrr/dist/starrr.css" rel="stylesheet">
	<!-- bootstrap-daterangepicker -->
	<link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

	<!-- Custom Theme Style -->

	<!-- Bootstrap -->
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css" integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<div class="col-md-3 left_col">
				<div class="left_col scroll-view">
					<div class="navbar nav_title" style="border: 0;">
						<a href="index.php" class="site_title"> <span><?= $rwcomp['comp_name']; ?></span></a>
					</div>

					<div class="clearfix"></div>

					<!-- menu profile quick info -->
					<div class="profile clearfix">
						<div class="profile_pic">
							<img src="images/logo.png" alt="..." class="img-circle profile_img">
						</div>
						<div class="profile_info">
							<span>Welcome,</span>
							<h2 style="text-align:left"><?= $user_name; ?></h2>
						</div>
					</div>
					<!-- /menu profile quick info -->

					<br />

					<!-- sidebar menu -->
					<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
						<div class="menu_section" style="    margin-top: -30px;">
							<!-- <h3 style="margin-bottom: 22px; margin-top: -14px;"><?= $user_name ?></h3> -->
							<ul class="nav side-menu">
								<?php
									function getModules($user_type_id, $connect) {
										if ($user_type_id == 1) {
											$query = "SELECT `module_id`, `module_name`, `module_url`, `icon` FROM `all_modules` ORDER BY sequence";
										} else {
											$query = "SELECT m.module_url, a.module_id, m.icon, m.module_name FROM assign_module a 
													INNER JOIN all_modules m ON a.module_id = m.module_id 
													WHERE a.role_id = $user_type_id AND a.All_access = '1' 
													GROUP BY m.module_id ORDER BY m.sequence";
										}
										return mysqli_query($connect, $query);
									}

									function getSubmodules($module_id, $user_type_id, $connect) {
										if ($user_type_id == 1) {
											$query = "SELECT `submodule_id`, `submodule_name`, `submodule_url`, `module_id` FROM all_submodule 
													WHERE module_id = $module_id ORDER BY sequence";
										} else {
											$query = "SELECT a.submodule_id, a.submodule_name, a.submodule_url FROM all_submodule a 
													INNER JOIN assign_submodule s ON a.submodule_id = s.submodule_id 
													WHERE s.role_id = $user_type_id AND s.All_access = '1' AND a.module_id = $module_id 
													GROUP BY s.submodule_id ORDER BY a.sequence";
										}
										return mysqli_query($connect, $query);
									}

									function renderMenu($modules, $user_type_id, $connect, $current_url,$porequestcount,
										 $poapprovaldetails, $Proapprovaldetails, $prorequestcount, $POPaymentRequestCount, 
										 $POPaymentApprovedDetailsCount, $POPaymentTTRequestCount, $POPaymentTTApprovedDetailsCount) {
										while ($module_row = mysqli_fetch_array($modules)) {
											$submodules = getSubmodules($module_row['module_id'], $user_type_id, $connect);
											$is_main_active = false;
											$submodule_html = '';

											if (mysqli_num_rows($submodules) > 0) {
												while ($submodule_row = mysqli_fetch_array($submodules)) {
													$is_active = ($submodule_row['submodule_url'] == $current_url);
													if ($is_active) {
														$is_main_active = true;
													}
													$porequestcountbadge='';
													if($user_type_id==1 && $porequestcount >0 && $submodule_row['submodule_name']=="Purchase Order Requests"){ 
														$porequestcountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$porequestcount</span>";
													}
													$poapprovaldetailsbadge='';
													if($user_type_id==72 && $poapprovaldetails >0 && $submodule_row['submodule_name']=="Purchase Order"){ 
														$poapprovaldetailsbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$poapprovaldetails</span>";
													}
													$Proapprovaldetailsbadge='';
													if($user_type_id==71 && $Proapprovaldetails >0 && $submodule_row['submodule_name']=="Pro-Forma Invoice"){ 
														$Proapprovaldetailsbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$Proapprovaldetails</span>";
													}
													$prorequestcountbadge='';
													if($user_type_id==1 && $prorequestcount >0 && $submodule_row['submodule_name']=="Pro-Forma Requests"){ 
														$prorequestcountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$prorequestcount</span>";
													}
													$POPaymentRequestCountbadge='';
													if($user_type_id==1 && $POPaymentRequestCount >0 && $submodule_row['submodule_name']=="Purchase Payment Details"){ 
														$POPaymentRequestCountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$POPaymentRequestCount</span>";
													}
													$POPaymentApprovedDetailsCountbadge='';
													if($user_type_id==73 && $POPaymentApprovedDetailsCount >0 && $submodule_row['submodule_name']=="Purchase Payment Details"){ 
														$POPaymentApprovedDetailsCountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$POPaymentApprovedDetailsCount</span>";
													}
													$POPaymentTTRequestCountbadge='';
													if($user_type_id==1 && $POPaymentTTRequestCount >0 && $submodule_row['submodule_name']=="Purchase Payment Details"){ 
														$POPaymentTTRequestCountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$POPaymentTTRequestCount</span>";
													}
													$POPaymentTTApprovedDetailsCountbadge='';
													if($user_type_id==73 && $POPaymentTTApprovedDetailsCount >0 && $submodule_row['submodule_name']=="Purchase Payment Details"){ 
														$POPaymentTTApprovedDetailsCountbadge ="<span class='badge bg-green btn-round' style='padding: 7px 13px 0px 9px;height: 24px;width: 22px;margin-top: 5px;'>$POPaymentTTApprovedDetailsCount</span>";
													}
													$submodule_html .= '
														<li class="' . ($is_active ? 'active' : '') . '" style="display: flex">
														<a href="' . $submodule_row['submodule_url'] . '">' . $submodule_row['submodule_name'] . '</a>'.
														$porequestcountbadge.''. $poapprovaldetailsbadge.''.$Proapprovaldetailsbadge.''.$prorequestcountbadge.
														''.$POPaymentRequestCountbadge.''.$POPaymentApprovedDetailsCountbadge.''.$POPaymentTTRequestCountbadge.
														''.$POPaymentTTApprovedDetailsCountbadge.'</li>';
												}
											}

											$is_active = ($module_row['module_url'] == $current_url) || $is_main_active;
											if (mysqli_num_rows($submodules) == 0 && !empty($module_row['module_url'])) {
													echo '<li class="' . ($is_active ? 'active open' : '') . ' ">
																<a href="' . $module_row["module_url"] . '">' . $module_row["icon"] . ' ' . $module_row["module_name"] . '</a>
															</li>';
											}
											else
											{
												echo '
												<li class="' . ($is_active ? 'active open' : '') . '">
													<a>' . $module_row["icon"] . ' ' . $module_row["module_name"] . ' <span class="fa fa-chevron-down"></span></a>
													<ul class="nav child_menu">
														' . $submodule_html . '
													</ul>
												</li>';
											}
										}
									}

									$user_type_id = $_SESSION['user_type_id'];
									$modules = getModules($user_type_id, $connect);

									if (mysqli_num_rows($modules) > 0) {
										renderMenu($modules, $user_type_id, $connect, $current_url, $porequestcount,
										 $poapprovaldetails, $Proapprovaldetails, $prorequestcount, $POPaymentRequestCount, 
										 $POPaymentApprovedDetailsCount, $POPaymentTTRequestCount, $POPaymentTTApprovedDetailsCount);
									}
								?>								
							</ul>
						</div>
						
					</div>

					<div class="sidebar-footer hidden-small">
						<a data-toggle="tooltip" data-placement="top" title="Settings">
							<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="FullScreen">
							<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="Lock">
							<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="Logout" href="../logout.php">
							<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
						</a>
					</div>
					<!-- /menu footer buttons -->
				</div>
			</div>

			<!-- top navigation -->
			<div class="top_nav">
				<div class="nav_menu">
					<div class="nav toggle">
						<a id="menu_toggle"><i class="fa fa-bars"></i></a>
					</div>
					<nav class="nav navbar-nav">
						<ul class=" navbar-right">
							<li class="nav-item dropdown open" style="padding-left: 15px;">
								<a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
									<img src="images/logo.png" alt="">Tej IT Solutions
								</a>
								<div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="javascript:;"> Profile</a>
									<a class="dropdown-item" href="javascript:;">
										<span class="badge bg-red pull-right">50%</span>
										<span>Settings</span>
									</a>
									<a class="dropdown-item" href="javascript:;">Help</a>
									<a class="dropdown-item" href="../logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
								</div>
							</li>
							 <!-- purchase order notification -->
							<?php if($user_type_id==1 && $porequestcount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Requests" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $porequestcount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwporequest = mysqli_fetch_assoc($rstporequest))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwporequest['po_date'].'</span>
															PO No : '.$rwporequest['po_custom_number'].' <br>Amount : '.$rwporequest['total_amt'].' '.$rwporequest['currency_code'].'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_order_requests.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>
							<?php if($user_type_id==72 && $poapprovaldetails >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Approval Details" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green"><?= $poapprovaldetails; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list" role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwpoapprovaldetails = mysqli_fetch_assoc($rstpoapprovalDetails))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												if($rwpoapprovaldetails['purchase_order_status']==1){ $POStatus = "<span style='color:green;'>Approved</span>"; }
												if($rwpoapprovaldetails['purchase_order_status']==2){ $POStatus = "<span style='color:red;'>Rejected</span>"; }
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwpoapprovaldetails['po_date'].'</span>
															PO No : '.$rwpoapprovaldetails['po_custom_number'].' <br>Amount : '.$rwpoapprovaldetails['total_amt'].' '.$rwpoapprovaldetails['currency_code'].'
															<br>Status : '.$POStatus.'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_orders.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>

							 <!-- Proforma  notification -->
							<?php if($user_type_id==1 && $prorequestcount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2 mr-3">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PI Requests" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $prorequestcount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwProrequest = mysqli_fetch_assoc($rstProrequest))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PI Date : '.$rwProrequest['pi_invoice_date'].'</span>
															PI No : '.$rwProrequest['pi_custom_number'].' <br>Amount : '.$rwProrequest['grand_total'].' '.$rwProrequest['currency_code'].'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="pro-forma-requests.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>
							<?php if($user_type_id==71 && $Proapprovaldetails >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PI Approval Details" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green"><?= $Proapprovaldetails; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list" role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwProapprovaldetails = mysqli_fetch_assoc($rstProapprovaldetails))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												if($rwProapprovaldetails['pro_forma_status']==1){ $ProStatus = "<span style='color:green;'>Approved</span>"; }
												if($rwProapprovaldetails['pro_forma_status']==2){ $ProStatus = "<span style='color:red;'>Rejected</span>"; }
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PI Date : '.$rwProapprovaldetails['pi_invoice_date'].'</span>
															PI No : '.$rwProapprovaldetails['pi_custom_number'].' <br>Amount : '.$rwProapprovaldetails['grand_total'].' '.$rwProapprovaldetails['currency_code'].'
															<br>Status : '.$ProStatus.'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="pro-forma.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>

							<!-- purchase payment request notification-->
							<?php if($user_type_id==1 && $POPaymentRequestCount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2 mr-3">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Payment Requests" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $POPaymentRequestCount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwPOPaymentRequest = mysqli_fetch_assoc($rstPOPaymentRequest))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwPOPaymentRequest['paid_date'].'</span>
															PO No : '.$rwPOPaymentRequest['po_custom_number'].' <br>Paid Amount : '.$rwPOPaymentRequest['paid_amount'].' '.$rwPOPaymentRequest['currency_code'].'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_receipt_payment.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>
							<?php if($user_type_id==73 && $POPaymentApprovedDetailsCount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2 mr-3">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Payment Details" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $POPaymentApprovedDetailsCount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										<?php 
											while($rwPOPaymentApprovedDetails = mysqli_fetch_assoc($rstPOPaymentApprovedDetails))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												if($rwPOPaymentApprovedDetails['approval_status']==1){ $PIPaymentStatus = "<span style='color:green;'>Approved</span>"; }
												if($rwPOPaymentApprovedDetails['approval_status']==0){ $PIPaymentStatus = "<span style='color:red;'>Rejected</span>"; }
												
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwPOPaymentApprovedDetails['paid_date'].'</span>
															PO No : '.$rwPOPaymentApprovedDetails['po_custom_number'].' <br>Paid Amount : '.$rwPOPaymentApprovedDetails['paid_amount'].' '.$rwPOPaymentApprovedDetails['currency_code'].'
															<br>Status : '.$PIPaymentStatus.'
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_receipt_payment.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>

							<!-- purchase payment tt notification -->
							<?php if($user_type_id==1 && $POPaymentTTRequestCount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2 mr-3">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Payment Transaction Requests" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $POPaymentTTRequestCount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwPOPaymentTTRequest = mysqli_fetch_assoc($rstPOPaymentTTRequest))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwPOPaymentTTRequest['paid_date'].'</span>
															PO No : '.$rwPOPaymentTTRequest['po_custom_number'].' <br>Paid Amount : '.$rwPOPaymentTTRequest['paid_amount'].' '.$rwPOPaymentTTRequest['currency_code'].'
															<br>TT Copy : <a href="production/'.$rwPOPaymentTTRequest['TransactionProof'].'" target="_blank" class="btn btn-sm btn-round btn-success"><i class="fa fa-eye" style="font-size: 10px;color: white;"></i></a>
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_receipt_payment.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>
							<?php if($user_type_id==73 && $POPaymentTTApprovedDetailsCount >0){ ?>	
								<li role="presentation" class="nav-item dropdown open mt-2 mr-3">
									<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" data-placement="top" title="PO Payment Transaction Details" aria-expanded="false">
										<i class="fa fa-envelope-o"></i>
										<span class="badge bg-green" ><?= $POPaymentTTApprovedDetailsCount; ?></span>
									</a>
									<ul class="dropdown-menu list-unstyled msg_list"  role="menu" aria-labelledby="navbarDropdown1">
										
										<?php 
											while($rwPOPaymentTTApprovedDetails = mysqli_fetch_assoc($rstPOPaymentTTApprovedDetails))
											{
												// <span>'.$rwporequest['client_name'].'</span>
												if($rwPOPaymentTTApprovedDetails['payment_status']==1){ $PIPaymentTTStatus = "<span style='color:green;'>Completed</span>"; }
												if($rwPOPaymentTTApprovedDetails['payment_status']==-2){ $PIPaymentTTStatus = "<span style='color:red;'>Rejected</span>"; }
												
												echo '<li class="nav-item">
													<a class="dropdown-item">
														<span>
															<span class="time">PO Date : '.$rwPOPaymentTTApprovedDetails['paid_date'].'</span>
															PO No : '.$rwPOPaymentTTApprovedDetails['po_custom_number'].' <br>Paid Amount : '.$rwPOPaymentTTApprovedDetails['paid_amount'].' '.$rwPOPaymentTTApprovedDetails['currency_code'].'
															<br>Status : '.$PIPaymentTTStatus.'
															<br>TT Copy : <a href="production/'.$rwPOPaymentTTApprovedDetails['TransactionProof'].'" target="_blank" class="btn btn-sm btn-round btn-success"><i class="fa fa-eye" style="font-size: 10px;color: white;"></i></a>
														</span>
													</a>
												</li>';
											}
										?>
										<li class="nav-item">
											<div class="text-center">
												<a class="dropdown-item" href="purchase_receipt_payment.php" target="_blank">
													<strong>See All Alerts</strong>
													<i class="fa fa-angle-right"></i>
												</a>
											</div>
										</li>
									</ul>
								</li>
							<?php } ?>

							<!-- branch switch button -->
							<li role="presentation" class="nav-item dropdown open">
								<?php
                                    $query5=mysqli_query($connect,"SELECT s.branch_id, s.user_id FROM branch_switcher s WHERE s.user_id  = '$user_id' ");
                                    if(mysqli_num_rows($query5)>0){
                                    while ($row5=mysqli_fetch_array($query5)) {
                                        if($row5['branch_id'] == $branch_id)
                                        {
                                        $disabled = "disabled";$onclick = "";
                                        }
                                        else
                                        {
                                        $disabled = "";$onclick = 'get_this_id('.$row5['branch_id'].') ';
                                        }
                                        $query6=mysqli_query($connect,"SELECT branch_name FROM branch_master WHERE branch_id = ".$row5['branch_id']." ");
                                        if(mysqli_num_rows($query6)>0){
                                        while ($row6=mysqli_fetch_array($query6)) {

                                            echo "<button ".$disabled." onclick ='".$onclick."' class='btn btn-round btn-sm btn-primary' style='color:white'>".$row6['branch_name']."</button>&nbsp;";

                                        }
                                        }
                                    }
                                    }
                                ?>
							</li>
						</ul>
					</nav>
				</div>
			</div>

