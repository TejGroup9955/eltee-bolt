<?php
  include_once('header.php');
  if($user_type_id== 1 )
  {
    $rstTotalSalesScroll = mysqli_query($connect,"SELECT SUM(p.grand_total) as TotalSale,cm.currency_code,p.currency_id 
    FROM pro_forma_head p inner join country_master cm on cm.id=p.currency_id where p.branch_id='$branch_id' 
    and p.active_status='1' group by p.currency_id");

    $rsTotalPurchaseScroll = mysqli_query($connect,"SELECT SUM(p.grand_total) as TotalPurchase,cm.currency_code FROM purchase_order p 
    inner join country_master cm on cm.id=p.currency_id
    WHERE p.branch_id = '$branch_id' AND p.active_status = 1 group by p.currency_id");

    $rstReceivable = mysqli_query($connect,"
      SELECT 
        cm.currency_code,
        p.currency_id,
        SUM(p.grand_total) as TotalSale, 
        SUM(pp.paid_amount) as TotalPaid 
      FROM pro_forma_head p 
      LEFT JOIN pro_forma_receipt_payment pp ON pp.pi_no = p.pi_no
      INNER JOIN country_master cm ON cm.id = p.currency_id
      WHERE p.branch_id = '$branch_id' AND p.active_status = '1'
      GROUP BY p.currency_id
    ");

    $rstPayable = mysqli_query($connect,"
      SELECT 
        cm.currency_code,
        p.currency_id,
        SUM(p.grand_total) as TotalPurchase, 
        SUM(pp.paid_amount) as TotalPaid 
      FROM purchase_order p 
      LEFT JOIN purchase_order_receipt_payment pp ON pp.po_no = p.po_id
      INNER JOIN country_master cm ON cm.id = p.currency_id
      WHERE p.branch_id = '$branch_id' AND p.active_status = '1'
      GROUP BY p.currency_id
    ");

    // $TotalProfit = $rwTotalSales['TotalSale'] - $rwTotalPurchase['TotalPurchase'];
    $rstTotalSales = mysqli_query($connect,"SELECT SUM(p.grand_total) as TotalSale FROM pro_forma_head p where 
    p.branch_id='$branch_id' and p.active_status='1'");
    $rwTotalSales = mysqli_fetch_assoc($rstTotalSales);

    $rsTotalPurchase = mysqli_query($connect,"SELECT SUM(p.grand_total) as TotalPurchase FROM purchase_order p 
    WHERE p.branch_id = '$branch_id' AND p.active_status = 1 ");
    $rwTotalPurchase = mysqli_fetch_assoc($rsTotalPurchase);

    $rstProductSales = mysqli_query($connect, "SELECT c.countryName,SUM(p.grand_total) as product_total FROM pro_forma_head p inner join country_master c on c.id=p.country_id where p.branch_id='$branch_id' and p.active_status='1' GROUP BY c.id");
    $productData = [];
    $colors = ['blue', 'green', 'purple', 'aero', 'red'];
    $i = 0;

    while ($row = mysqli_fetch_assoc($rstProductSales)) {
        $countryName = $row['countryName'];
        $productTotal = $row['product_total'];
        $percent = round(($productTotal / $rwTotalSales['TotalSale']) * 100);
        $productData[] = [
            'name' => $countryName,
            'percent' => $percent,
            'color' => $colors[$i % count($colors)]
        ];
        $i++;
    }

    $rstProductPurchase = mysqli_query($connect, "SELECT c.countryName,SUM(p.grand_total) as product_total FROM purchase_order p inner join country_master c on c.id=p.country_id where p.branch_id='$branch_id' and p.active_status='1' GROUP BY c.id");
    $purchaseproductData = [];
    $colors = ['blue', 'green', 'purple', 'aero', 'red'];
    $i = 0;

    while ($rwproductpurchase = mysqli_fetch_assoc($rstProductPurchase)) {
        $countryName = $rwproductpurchase['countryName'];
        $productTotal = $rwproductpurchase['product_total'];
        $percent = round(($productTotal / $rwTotalPurchase['TotalPurchase']) * 100);
        $purchaseproductData[] = [
            'name' => $countryName,
            'percent' => $percent,
            'color' => $colors[$i % count($colors)]
        ];
        $i++;
    }

    $rstSales = mysqli_query($connect, "
        SELECT DATE_FORMAT(pi_invoice_date, '%Y-%m') as month, SUM(grand_total) as total 
        FROM pro_forma_head 
        WHERE branch_id='$branch_id' AND active_status='1' 
        GROUP BY month
    ");

    // Query: Monthly purchases
    $rstPurchase = mysqli_query($connect, "
        SELECT DATE_FORMAT(po_date, '%Y-%m') as month, SUM(grand_total) as total 
        FROM purchase_order 
        WHERE branch_id='$branch_id' AND active_status='1' 
        GROUP BY month
    ");

    // Step 1: Collect all months from both result sets
    $allMonths = [];

    // Fill purchases
    $purchasesDataMap = [];
    while ($row = mysqli_fetch_assoc($rstPurchase)) {
        $month = $row['month'];
        $timestamp = strtotime($month . '-01') * 1000;
        $purchasesDataMap[$month] = (float)$row['total'];
        $allMonths[$month] = $timestamp;
    }

    // Fill sales
    $salesDataMap = [];
    while ($row = mysqli_fetch_assoc($rstSales)) {
        $month = $row['month'];
        $timestamp = strtotime($month . '-01') * 1000;
        $salesDataMap[$month] = (float)$row['total'];
        $allMonths[$month] = $timestamp;
    }

    // Sort all months chronologically
    ksort($allMonths);

    // Step 2: Create aligned arrays
    $salesData = [];
    $purchasesData = [];

    foreach ($allMonths as $month => $timestamp) {
        $sales = isset($salesDataMap[$month]) ? $salesDataMap[$month] : 0;
        $purchase = isset($purchasesDataMap[$month]) ? $purchasesDataMap[$month] : 0;

        $salesData[] = [$timestamp, $sales];
        $purchasesData[] = [$timestamp, $purchase];
    }

    // Step 3: Output to JS
    echo "<script>
        var arr_data1 = {
            label: 'Sales',
            data: " . json_encode($salesData) . "
        };
        var arr_data2 = {
            label: 'Purchases',
            data: " . json_encode($purchasesData) . "
        };
    </script>";


    $productWiseSales = [];
    $query = "
        SELECT p.product_name, SUM(pd.total_amt) AS TotalSum 
        FROM pro_forma_head_details pd
        INNER JOIN pro_forma_head ph ON ph.pi_no = pd.pi_no
        INNER JOIN product_master p ON p.product_id = pd.product_id
        WHERE ph.year_id = 5
        GROUP BY pd.product_id
        ORDER BY TotalSum DESC LIMIT 5
    ";

    $result = $connect->query($query);
    $maxTotal = 0;

    while ($row = $result->fetch_assoc()) {
        $productWiseSales[] = $row;
        if ($row['TotalSum'] > $maxTotal) {
            $maxTotal = $row['TotalSum'];
        }
    }
  }
?>

<style>
  .count {
      font-size: 21px!important;
  }
</style>
<div class="right_col" role="main">
    <?php if($user_type_id==1){ ?>
        <div class="col-md-12" style="display: inline-block;" >
          <div class="tile_count">
            <div class="col-md-6">
                <span class="count_top"><i class="fa fa-user"></i> Total sales</span>
                <div class="row">
                  <?php
                      while($rwTotalSalesScroll = mysqli_fetch_assoc($rstTotalSalesScroll))
                      {
                        $currency_code = $rwTotalSalesScroll['currency_code'];
                        $total_sale = number_format($rwTotalSalesScroll['TotalSale']??0);
                      echo '<div class="col-md-3 count green">
                            <div class="card" style="margin:5px; padding:5px; box-shadow:none; border:1px solid #ddd;">
                              <div class="card-body" style="font-weight:bold;padding:8px; font-size:14px; line-height:1.2; text-align:center;">
                                '.$currency_code.'<br>'.$total_sale.'
                              </div>
                            </div>
                          </div>';
                      }
                  ?>
                </div>
                <!-- <span class="count_bottom"><i class="green">4% </i> From last Week</span> -->
            </div>
            <div class="col-md-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> Total Purchase</span>
                <div class="row">
                  <?php
                      while($rwTotalPurchaseScroll = mysqli_fetch_assoc($rsTotalPurchaseScroll))
                      {
                        $currency_code_purchase = $rwTotalPurchaseScroll['currency_code'];
                        $TotalPurchase = number_format($rwTotalPurchaseScroll['TotalPurchase']??0);
                        echo '<div class="col-md-3 count red">
                          <div class="card" style="margin:5px; padding:5px; box-shadow:none; border:1px solid #ddd;">
                            <div class="card-body" style="padding:8px; font-size:14px; line-height:1.2; text-align:center;">
                              '.$currency_code_purchase.'<br>'.$TotalPurchase.'
                            </div>
                          </div>
                        </div>';
                      }
                  ?>
                </div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>3% </i> From last Week</span> -->
            </div>
            <div class="col-md-6">
              <span class="count_top"><i class="fa fa-user"></i> Total Receivables</span>
              <div class="row">
                <?php
                    while ($rwReceivable = mysqli_fetch_assoc($rstReceivable)) {
                        $currency_code = $rwReceivable['currency_code'];
                        $total_sale = $rwReceivable['TotalSale'] ?? 0;
                        $total_paid = $rwReceivable['TotalPaid'] ?? 0;
                        $remaining = number_format($total_sale - $total_paid);
                        echo '<div class="col-md-3 count green">
                          <div class="card" style="margin:5px; padding:5px; box-shadow:none; border:1px solid #ddd;">
                            <div class="card-body" style="font-weight:bold; padding:8px; font-size:14px; line-height:1.2; text-align:center;">
                              '.$currency_code.'<br>'.$remaining.'
                            </div>
                          </div>
                        </div>';
                    }
                ?>
              </div>
              <!-- <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span> -->
            </div>
            <div class="col-md-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Payables</span>
              <div class="row">
                <?php
                    while ($rwPayable = mysqli_fetch_assoc($rstPayable)) {
                        $currency_code = $rwPayable['currency_code'];
                        $total_purchase = $rwPayable['TotalPurchase'] ?? 0;
                        $total_paid = $rwPayable['TotalPaid'] ?? 0;
                        $remaining = number_format($total_purchase - $total_paid);
                        echo '<div class="col-md-3 count red">
                          <div class="card" style="margin:5px; padding:5px; box-shadow:none; border:1px solid #ddd;">
                            <div class="card-body" style="padding:8px; font-size:14px; line-height:1.2; text-align:center;">
                              '.$currency_code.'<br>'.$remaining.'
                            </div>
                          </div>
                        </div>';
                    }
                ?>
              </div>
              <!-- <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>12% </i> From last Week</span> -->
            </div>
            <!-- <div class="col-md-2   tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Net Profit</span>
              <div class="count"></div>
            </div> -->
            <!-- <div class="col-md-2 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Connections</span>
              <div class="count">7,325</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div> -->
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="dashboard_graph">

              <div class="row x_title">
                <div class="col-md-6">
                  <h6>Sale/Purchase Graph</h6>
                </div>
                <!-- <div class="col-md-6">
                  <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                    <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                  </div>
                </div> -->
              </div>

              <div class="col-md-9 col-sm-9 ">
                <div id="SalePurchaseChartData" class="demo-placeholder"></div>
              </div>
              <div class="col-md-3 col-sm-3  bg-white">
                <div class="x_title">
                  <h2>Top Users</h2>
                  <div class="clearfix"></div>
                </div>

                <div class="col-md-12 col-sm-12 ">
                  <?php
                    $rstTopSales = mysqli_query($connect,"SELECT SUM(pp.grand_total) as TotalSale, u.user_name FROM pro_forma_head pp
                    inner join user_master u  on u.user_id = pp.user_id where pp.branch_id='$branch_id' and pp.active_status='1' GROUP BY u.user_name ORDER BY TotalSale DESC LIMIT 5");
                    while($rwTopSales = mysqli_fetch_assoc($rstTopSales))
                    {
                      $TotalSaleuserwise = $rwTopSales['TotalSale']; 
                      $user_name = $rwTopSales['user_name']; 
                      $percentage = number_format(($TotalSaleuserwise / $rwTotalSales['TotalSale']) * 100);

                        echo '
                          <div class="mb-4 p-3 rounded shadow-sm border bg-light">
                              <div class="d-flex justify-content-between align-items-center mb-1">
                                  <h6 class="mb-0">
                                      <span class="badge bg-primary text-uppercase" style="color:white;">'.$user_name.'</span>
                                  </h6>
                                  <small class="text-muted fw-bold">$'.number_format($TotalSaleuserwise, 2).' </small>
                              </div>
                              <div class="progress" style="height: 18px;">
                                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" 
                                      style="width: '.$percentage.'%;" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100">
                                      '.$percentage.'%
                                  </div>
                              </div>
                          </div>';
                      }
                  ?>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>

        </div>
        <br />
        <div class="row">
          <div class="col-md-4 col-sm-4 ">
            <div class="x_panel tile fixed_height_350">
              <div class="x_title">
                <h2>Product Wise Sale</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Settings 1</a>
                        <a class="dropdown-item" href="#">Settings 2</a>
                      </div>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                  <h4>App Usage across versions</h4>
                  <?php
                    foreach ($productWiseSales as $item) {
                        $productName = htmlspecialchars($item['product_name']);
                        $totalSum = number_format($item['TotalSum']);
                        $percentage = $maxTotal > 0 ? round(($item['TotalSum'] / $maxTotal) * 100) : 0;

                        echo "
                        <div class='row'>
                            <div class='col-md-12'>
                                <span>$productName</span>
                            </div><br>
                            <div class='col-md-8'>
                                <div class='progress'>
                                    <div class='progress-bar bg-green' role='progressbar' 
                                        aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100' 
                                        style='width: $percentage%;'>
                                        <span class='sr-only'>$percentage% Complete</span>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-4'>
                                <span style='font-size:13px;'>$totalSum</span>
                            </div>
                            <hr>
                            <div class='clearfix'></div>
                        </div>
                        ";
                    }
                  ?>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-4 ">
            <div class="x_panel tile fixed_height_350 overflow_hidden">
              <div class="x_title">
                <h2>Country wise Sales</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link" style="padding-left: 45px;"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <!-- <li><a class="close-link"><i class="fa fa-close"></i></a></li> -->
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="" style="width:100%">
                  <tr>
                    <th style="width:55%;">
                      <p>Top 5</p>
                    </th>
                    <th>
                      <div class="col-lg-7 col-md-7 col-sm-7 ">
                        <p class="">Device</p>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-5 ">
                        <p class="">Progress</p>
                      </div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <canvas class="canvasDoughnutSales" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                    </td>
                    <td>
                      <table class="tile_info">
                        <?php foreach ($productData as $item): ?>
                          <tr>
                            <td>
                              <p><i class="fa fa-square <?= $item['color'] ?>"></i><?= htmlspecialchars($item['name']) ?></p>
                            </td>
                            <td><?= $item['percent'] ?>%</td>
                          </tr>
                        <?php endforeach; ?>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-4 ">
            <div class="x_panel tile fixed_height_350 overflow_hidden">
              <div class="x_title">
                <h2>Country wise Purchase</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link" style="padding-left: 45px;"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <!-- <li><a class="close-link"><i class="fa fa-close"></i></a> -->
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="" style="width:100%">
                  <tr>
                    <th style="width:55%;">
                      <p>Top 5</p>
                    </th>
                    <th>
                      <div class="col-lg-7 col-md-7 col-sm-7 ">
                        <p class="">Device</p>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-5 ">
                        <p class="">Progress</p>
                      </div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <canvas class="canvasDoughnutpurchase" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                    </td>
                    <td>
                      <table class="tile_info">
                        <?php foreach ($purchaseproductData as $item): ?>
                          <tr>
                            <td>
                              <p><i class="fa fa-square <?= $item['color'] ?>"></i><?= htmlspecialchars($item['name']) ?></p>
                            </td>
                            <td><?= $item['percent'] ?>%</td>
                          </tr>
                        <?php endforeach; ?>
                      </table>

                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>  
    <?php } ?>
</div>
<?php include_once('footer.php'); ?>
<script>
  var chart_plot_01_settings = {
    series: {
        lines: { show: false, fill: true },
        splines: { show: true, tension: 0.4, lineWidth: 1, fill: 0.4 },
        points: { radius: 3, show: true },
        shadowSize: 2
    },
    grid: {
        verticalLines: true,
        hoverable: true,
        clickable: true,
        tickColor: "#d5d5d5",
        borderWidth: 1,
        color: '#fff'
    },
    colors: ["#26B99A", "#03586A"],
    xaxis: {
        tickColor: "rgba(51, 51, 51, 0.06)",
        mode: "time",
        tickSize: [1, "month"],
        axisLabel: "Month",
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 10
    },
    yaxis: {
        ticks: 8,
        tickColor: "rgba(51, 51, 51, 0.06)",
    },
    tooltip: true,
    tooltipOpts: {
        content: "%s: %y.2", // %s = series label, %y.2 = Y value with 2 decimals
        shifts: {
            x: 10,
            y: 20
        },
        defaultTheme: false
    },
    legend: {
        show: true,
        position: "ne",
        labelBoxBorderColor: "none",
        noColumns: 1,
        margin: [10, 10],
        backgroundColor: "#fff"
    }
  };


  if ($("#SalePurchaseChartData").length) {
    console.log(arr_data1);
    console.log(arr_data2);
      $.plot($("#SalePurchaseChartData"), [arr_data1, arr_data2], chart_plot_01_settings);
  }
</script>
<script>
  const doughnutData = {
    labels: <?= json_encode(array_column($productData, 'name')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($productData, 'percent')) ?>,
      backgroundColor: [
        '#3498DB', // blue
        '#26B99A', // green
        '#9B59B6', // purple
        '#BDC3C7', // aero
        '#E74C3C'  // red
      ]
    }]
  };

  const ctx = document.querySelector(".canvasDoughnutSales").getContext("2d");
  new Chart(ctx, {
    type: 'doughnut',
    data: doughnutData,
    options: {
      legend: {
        display: false
      },
      responsive: false
    }
  });
</script>
<script>
  const doughnutDataPurchase = {
    labels: <?= json_encode(array_column($purchaseproductData, 'name')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($purchaseproductData, 'percent')) ?>,
      backgroundColor: [
        '#3498DB', // blue
        '#26B99A', // green
        '#9B59B6', // purple
        '#BDC3C7', // aero
        '#E74C3C'  // red
      ]
    }]
  };

  const ctxpurchase = document.querySelector(".canvasDoughnutpurchase").getContext("2d");
  new Chart(ctxpurchase, {
    type: 'doughnut',
    data: doughnutDataPurchase,
    options: {
      legend: {
        display: false
      },
      responsive: false
    }
  });
</script>


