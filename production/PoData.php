// if(isset($_POST['tableData'])){
    //     $tableDataNew = json_decode($_POST['tableData'], true);
    //     foreach ($tableDataNew as $contact) {
    //         $product_id = $contact['product_id'];
    //         $quantity = $contact['quantity'];
    //         $rate = $contact['rate'];
    //         $totalamt = $contact['totalamt'];
    //         $gst_amount = $contact['gst_amount'];
    //         $totalweight = $contact['totalweight'];
    //         $rateperton = $contact['rateperton'];
    //         $weightperpack = $contact['weightperpack'];
    //         if(isset($ProformaId))
    //         {
    //             $total_new_qty = 0; 
    //             foreach($ProformaId as $PINo)
    //             {
    //                 $rstpi = mysqli_query($connect,"select pro_forma_head_details_id,product_id,no_of_bags from pro_forma_head_details
    //                 where pi_no='$PINo' and product_id='$product_id' ");
    //                 if(mysqli_num_rows($rstpi)>0)
    //                 {
    //                     $rwpidetails = mysqli_fetch_assoc($rstpi);
    //                     $product_id_Save=$rwpidetails['product_id'];
    //                     $no_of_bags=$rwpidetails['no_of_bags'];
    //                     $pro_forma_head_details_id=$rwpidetails['pro_forma_head_details_id'];

    //                     $total_new_qty += $quantity;
    //                     echo "Exist qty: " . $no_of_bags . "<br>";
    //                     echo "Update qty: " . $quantity . "<br>";

    //                     // if ($no_of_bags > $quantity) {
    //                         $po_used_qty = $quantity - $no_of_bags;
    //                         // $rstproforma = mysqli_query($connect,"update pro_forma_head_details set po_total_qty='$quantity',
    //                         // po_used_qty='$po_used_qty' where pro_forma_head_details_id='$pro_forma_head_details_id' ");
    //                         echo  "update pro_forma_head_details set po_total_qty='$quantity',
    //                         po_used_qty='$po_used_qty' where pro_forma_head_details_id='$pro_forma_head_details_id' ";
    //                     // }
    //                 }
    //             }
    //             // if ($total_new_qty > $quantity) {
    //             //     echo "⚠️ New qty for product_id $product_id exceeds the original qty. Update aborted.<br>";
    //             // }
    //         }
    //         // $query =" INSERT INTO `purchase_order_details`(purchase_order_id,
    //         // product_id, each_bag_weight, no_of_bags, total_weight, rate,rateperton,packaging_type,
    //         // gst,total_amt,packaging_id,pi_no) 
    //         // VALUES('$po_no_new','$product_id','$weightperpack','$quantity','$totalweight','$rate',
    //         // '$rateperton', '$packagingTypeName','$gst_amount','$totalamt','$packaging_id','$Pi_No_Save')";
    //         // $query_res2 = $connect->query($query);       
    //         $i++;
    //     }
    // }