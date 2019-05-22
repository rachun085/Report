<?php
	require_once __DIR__ . '/vendor/autoload.php';
    require ('connect.php');
    
    

    function DateThai($datetime){

        $strYear = date("Y",strtotime($datetime))+543;
        $strMonth= date("m",strtotime($datetime));
        $strDay= date("d",strtotime($datetime));
        return "$strDay/$strMonth/$strYear";
    }


    //ท้องที่ 0

function printpig($pigId,$con) {
    
    // global $sql,$result,$style,$result2,$head,$data,$content_th,$end,$content_header;
    
    $sql = "SELECT detail_event.pig_id,event_name,event_recorddate,pig_recorddate,pig_breeder 
    FROM detail_event
    INNER JOIN event_list ON (detail_event.event_id = event_list.event_id) 
    LEFT JOIN profile_pig ON (detail_event.pig_id = profile_pig.pig_id) 
    LEFT JOIN farm ON (profile_pig.farm_id = farm.farm_id) 
    WHERE detail_event.event_id = 1
    AND profile_pig.pig_id = '$pigId' ";

    
    $result = mysqli_query($con, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($beforerow = mysqli_fetch_row($result)) {
                        $pig_recorddate = $beforerow[3]; 
                    }
                    $content_th .= '
                    <table border="1" width="100%" style="border-collapse: collapse;font-size:16pt;">
                    <thead>
                <tr>
                    <th>ท้องที่</th>
                    <th width="20%">วันที่</th>
                    <th width="30%">เหตุการณ์</th>
                    <th width="50%">ข้อมูลเกี่ยวกับเหตุการณ์</th>
                </tr>
                <tr>
                    <td rowspan="10" align="center">0</td>
                    <td align="center">'.DateThai($pig_recorddate).'</td>
                    <td align="center">เข้าฝูง</td>
                    <td align="center">ท้องที่: 0 มาจาก:</td>
                </tr>
                </thead>
                <tbody>';
                
                $result2 = mysqli_query($con, $sql);
                while($row = mysqli_fetch_array($result2)) {  
                    $content_th .=
                    '<tr>
                        <td align="center">'.DateThai($row['event_recorddate']).'</td>
                        <td align="center">'.$row['event_name'].'</td>
                        <td align="center">พ่อพันธุ์ : '.$row['pig_breeder'].'</td>
                    </tr>
                '; 
                
                
             }
          
        }

            $sql2 = "SELECT event_name,pregnant.pig_id,pregnant.event_recorddate,detail_event.event_recorddate AS DATE,pig_alive,pig_die,pig_seedlings,pig_breeder,pig_allweight,pregnant.pig_amount_pregnant,pig_amountofwean,result_pregnant,disease_name,note,pig_resultofexclude,pig_reasonofexclude 
            FROM pregnant 
            inner join detail_event on (pregnant.pig_id = detail_event.pig_id) 
            AND (pregnant.pig_amount_pregnant = detail_event.pig_amount_pregnant) 
            inner join event_list ON (detail_event.event_id = event_list.event_id)
            WHERE pregnant.pig_id = '$pigId'  
            ORDER BY pregnant.`pig_id` ASC, pregnant.pig_amount_pregnant ASC ";
        
            $result3 = mysqli_query($con, $sql2);
            $content_header = "";
            
                if (mysqli_num_rows($result3) > 0) {
                    $pregnant = 0;
   
                    while($row2 = mysqli_fetch_array($result3)) {
                        $fetch_pregnant = $row2['pig_amount_pregnant'];
                        

                        if($pregnant != $fetch_pregnant){
                            if($content_header != ""){
                                $content_header .= '</tbody></table>';
                            }
                            
                            $pregnant = $fetch_pregnant;
                            $content_header .= '
                            <table border="1" width="100%" style="border-collapse: collapse;font-size:16pt;">
                            <thead>
                                <tr>
                                    <th>ท้องที่</th>
                                    <th width="20%">วันที่</th>
                                    <th width="30%">เหตุการณ์</th>
                                    <th width="50%">ข้อมูลเกี่ยวกับเหตุการณ์</th>
                                </tr>
                            </thead>
                            <tbody>
                            ';
                        }
                        
                        $content_header .= '<tr>
                            <td align="center">'.$row2['pig_amount_pregnant'].'</td>
                            <td align="center">'.DateThai($row2['event_recorddate']).'</td>';
                            $content_header .= '<td align="center">' . $row2["event_name"] . "</td>";
                        if ($row2["event_name"]=="คลอด"){
                            $content_header .= '<td align="center">เป็น : '.$row2['pig_alive'].' ตาย : '.$row2['pig_die'].' มัมมี่ : '.$row2['pig_seedlings'].' รวม : '.$row2['pig_allweight'].'</td>';
                        } 
                        else if($row2["event_name"]=="ผสมพันธุ์"){
                            $content_header .= '<td align="center">พ่อพันธุ์ : ' . $row2["pig_breeder"] .'</td>';
                        }
                        else if($row2["event_name"]=="หย่านม"){
                            $content_header .= '<td align="center">จำนวน : '. $row2["pig_amountofwean"] . ' นน.รวม: '.$row2['pig_allweight']. '</td>';
                        }
                        else if($row2["event_name"]=="ตรวจท้อง"){
                            $content_header .= '<td align="center">ผลการตรวจ : '. $row2["result_pregnant"] . '</td>';
                        }
                        else if($row2["event_name"]=="ป่วยเป็นโรค"){
                            $content_header .= '<td align="center">อาการของโรค : '. $row2["disease_name"] . '</td>';
                        }
                        else if($row2["event_name"]=="ลูกหมูตาย"){
                            $content_header .= '<td align="center">จำนวน : '. $row2["pig_die"] . ' สาเหตุ: ' . $row2["note"]. '</td>';
                        }
                        else if($row2["event_name"]=="ฝากเลี้ยง"){
                            $content_header .= '<td align="center">ผลการตรวจ : '. $row2["result_pregnant"] . '</td>';
                        }
                        else if($row2["event_name"]=="คัดทิ้ง"){
                            $content_header .= '<td align="center">]ลักษณะ : '. $row2["pig_resultofexclude"] . ' สาเหตุ: '.$row2["pig_reasonofexclude"]. '</td>';
                        }
    
                        
                        $content_header .= "</tr>";
    
                        }
                        $content_header .= '</tbody></table>';
                    }

       
        
        

                $sql3 = "SELECT event_name,detail_event.pig_id,pig_amount_pregnant,pig_birthday,pig_idbreeder,pig_idbreeder2,pig_breed
                FROM detail_event
                inner join profile_pig on (detail_event.pig_id = profile_pig.pig_id) 
                inner join event_list ON (detail_event.event_id = event_list.event_id) 
                WHERE detail_event.pig_id = '$pigId'
                ORDER BY pig_amount_pregnant DESC , detail_id DESC limit 1";
    
                $fetch = mysqli_query($con, $sql3);
                while($rowhistory = mysqli_fetch_array($fetch)) {
                    $pig_amount_pregnant = $rowhistory["pig_amount_pregnant"];
                    $event_name =  $rowhistory["event_name"];
                    $pig_birthday =  $rowhistory["pig_birthday"];
                    $pig_idbreeder =  $rowhistory["pig_idbreeder"];
                    $pig_idbreeder2 =  $rowhistory["pig_idbreeder2"];
                    $pig_breed =  $rowhistory["pig_breed"];
                }

        


    $data .= '<table border=0 width="100%" style="font-size:16pt;">
    <tr>
            <td><b>เบอร์แม่ : </b></td>
            <td>'.$query_pigid.'</td>
            <td><b>ท้องที่ : </b></td>
            <td>'.$pig_amount_pregnant.'</td>
            <td><b>สถานภาพ : </b></td>';

            if($event_name == "คลอด"){
                $data .= '<td>ให้นม</td>';
            }else{
            $data .= '<td>'.$event_name.'</td>';
            }

    $data .= '</tr>
    <tr>
            <td><b>วันเกิด : </b></td>
            <td>'.DateThai($pig_birthday).'</td>
            <td><b>แม่ : </b></td>
            <td>'.$pig_idbreeder2.'</td>  
            <td><b>พ่อ:</b> </td>
			<td>'.$pig_idbreeder.'</td>
            <td><b>พันธุ์:</b></td>';
            if($pig_breed == ""){
                $data .= '<td>ไม่ระบุ</td>';
            }else{
                $data .= '<td>'.$pig_breed.'</td>';
            }
        
			    
            $data .= '</tr></table><br/><br/>';
        
$end = "</tbody></table><br/><br/><br/>";



    $style = '
<style>
    body{
        font-family: "thsarabun";
    }
</style>';


$head .= '<h2 style="text-align:center">ประวัติพ่อแม่พันธุ์</h2></br>';

// echo $style;
// echo $head;
// echo $data;
// echo $content_th;
// echo $end;
// echo $content_header;

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->setFooter('{PAGENO}');
        $mpdf->WriteHTML($style);
        $mpdf->WriteHTML($head);
        $mpdf->WriteHTML($data);

        $mpdf->WriteHTML($content_th);
        $mpdf->WriteHTML($end);
        $mpdf->WriteHTML($content_header);


        $mpdf->Output();
       

    } //end function


    $fetch_pigid = "SELECT DISTINCT pregnant.pig_id
                    FROM pregnant 
                    inner join detail_event on (pregnant.pig_id = detail_event.pig_id) 
                    AND (pregnant.pig_amount_pregnant = detail_event.pig_amount_pregnant) 
                    inner join event_list ON (detail_event.event_id = event_list.event_id) 
                    ORDER BY pregnant.`pig_id` ASC, pregnant.pig_amount_pregnant ASC";

    $result_pigid = mysqli_query($con, $fetch_pigid);
    $query_pigid;

    while($pd = mysqli_fetch_assoc($result_pigid)) {
        $query_pigid = $pd["pig_id"]; 

        printpig($query_pigid,$con);

    }

        mysqli_close($con);

?>

