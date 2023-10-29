<?php

namespace app\models;

    use yii\db\ActiveRecord;
    use yii\web\UploadedFile;
    use app\models\Count;
    use DateTime;
    include("PHPExcel.php");
    include("PHPExcel/Writer/Excel5.php");
    class UploadFileForm extends ActiveRecord
{
  /**
   * @var UploadedFile
   */
  public $file;
 
  public function rules()
  {
    return [
      ['file', 'file',
        'extensions' => ['xls', 'xlsx'],
        'checkExtensionByMimeType' => false,
        'maxSize' => 512000,
        'tooBig' => 'Limit is 500KB'
      ],
    ];
  }

  public function Namefile() // Метод парсит эксель файл и возвращает html-код таблицы эксель (переменная $report)
  {
    if ($this->validate()) {
    $objPHPExcel = \PHPExcel_IOFactory::load($this->file->tempName); //инициализация эксель файла
    // Библиотека PHPEXCEL плохо работает с Yii, если убрать \ перед PHPExcel_IOFactory, то не будет работать

    $now = new DateTime();
    $current_date = $now->format('Y-m-d');

    $Counts = new Count();
    $Counts = Count::findone(1);

    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) { // проходимся в цикле по таблице эксель
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
    // в переменную $report записываем значение эксель таблицы
    $report = '';
    $array_delivery[] = array();
    //$array_delivery['API000'] = "Отправление доставлено;Кознова;2022-04-15";
    $report .= '<div class="table-responsive">
    <table class="table">
  <thead>
    <tr>';
    //только для локального сервера, что исправить ошибку SSL
  /*$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
); */
    for ($row = 1; $row <= $highestRow; ++ $row) {
      // Добавляем назнания столбов, которые будем заполнять в шапку
    $status = 'Статус'; 
    $FIO = 'ФИО Получателя';
    $date_del = 'Дата доставки';
    $transport_company = 'ТК';
    $CSPP = 'ЦСПП';
        
        $report .= '<tr>';
        for ($col = 0; $col < $highestColumnIndex - 1; ++ $col) {
          $width = 'auto';
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            $dataType = \PHPExcel_Cell_DataType::dataTypeForValue($val);

            
            

            // корректировка длины каждой строки в столбце, где $col - номер столбца

          /*  if($col == 6){
              $width = '500px';
            }
            if($col == 0){
              $width = '100px';
            }
            if($col == 1 || $col == 2){
              $width = '200px';
            }
            if($col == 7){
              $width = '70px';
            }*/
            // Накладная в 3 столбце
            if($col == 3){
              $val = trim($val);
              if($val == 'Трек'){
                $report .= '<td> ' . $val . '</td>';
              }
              else{ ////
                  if(!empty($array_delivery[$val])){ //Если по данной накладной уже была проверка, то берем данные из массива
                  $pieces = explode(";", $array_delivery[$val]); //разбиваем строку
                    $status = $pieces[0];
                    $FIO = $pieces[1];
                    $date_del = $pieces[2];
                    $transport_company = $pieces[3];
                    $CSPP = $pieces[4];
                }
                else{



                $pr_val = mb_substr($val,0,1, "utf-8"); //возвращает первый символ накладной
                $nak_id = $val;
                if($pr_val == 'A' && strlen($val) == 11){ //Если ТК dimex
                  $transport_company = 'Dimex';
                  $TK_web_site = 'https://www.dimex.ws/';

                  if($Counts->Dimex_date == $current_date && $Counts->Dimex > 1000){
                      $status = 'За '.$current_date.' было более 500 запросов к ТК '.$transport_company.'. Просьба продолжить завтра';
                      $CSPP = '';
                      $FIO = "";
                      $date_del = "";
                      $date_del = "";
                      }
                      else{
                        if($Counts->Dimex_date != $current_date){
                            $Counts->Dimex = 0;
                            $Counts->Dimex_date = $current_date;
                        }
                        $Counts->Dimex = $Counts->Dimex + 1;
                        $Counts->save();
                      

                  sleep(0.1);

                  $info = file_get_contents('http://rus.tech-dimex.ru/api/tracing/tracingget?number='.$nak_id);
                  $dimex = json_decode($info, true);

                  if(empty($dimex)){ 
                  	//sleep(1);
                    $info = file_get_contents('http://rus.tech-dimex.ru/api/tracing/tracingget?number='.$nak_id);
                    $dimex = json_decode($info, true);
                  }

          

                  $i = 0;
                  do {
                      
                      if($i > 100){
                        break;
                      }
                      
                      $i++;
                  } while (!empty($dimex["data"]['tracing'][$i]['status']));

                  if(!empty($dimex["data"]['tracing'][$i-1]['status'])){
                        $status = $dimex["data"]['tracing'][$i-1]['status'];
                        $FIO = mb_convert_case($dimex["data"]['tracing'][$i-1]['comment'], MB_CASE_TITLE, "UTF-8");
                        $date_del = $dimex["data"]['tracing'][$i-1]['date'];

                        if(trim($status) == 'Отправление доставлено'){
                          $CSPP = 'Груз доставлен, накладная: '.$nak_id.', Получатель: '.$FIO.', Дата доставки: '.$date_del;
                        }
                        else{
                          $CSPP = 'Груз отправлен, статус отправления: '.$status.', Накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;
                        }

                        $array_delivery[$val] = $status.";".$FIO.";".$date_del.";".$transport_company.";".$CSPP;
                  }
                  elseif($i == 0){
                        $status = $dimex["data"]['tracing'][$i]['status'];
                        $FIO = $dimex["data"]['tracing'][$i]['comment'];
                        $date_del = $dimex["data"]['tracing'][$i]['date'];
                        $CSPP = 'Груз отправлен, статус отправления: '.$status.', Накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;

                        $array_delivery[$val] = $status.";".$FIO.";".$date_del.";".$transport_company.";".$CSPP;
                  }
                  else{
                    $status = 'Не удалось получить информацию';
                    $FIO = "";
                    $date_del = "";
                    $CSPP = "";
                    $array_delivery[$val] = $status.";".$FIO.";".$date_del.";".$transport_company.";".$CSPP;
                  }


               }
                  
                }
                elseif(($pr_val == 1 && strlen($val) == 10) || ($pr_val == 'L' && strlen($val) == 10)){ //Если ТК Major
                  $transport_company = 'Major Express';
                  //usleep(100);
                  $major_product = 1;
                  //product=1 - экспресс доставка, product=2 - сборные грузы
                  if($pr_val == 1){
                      $major_product = 1;
                      $TK_web_site = 'https://major-express.ru/ (Выбрать экспресс-доставка)';
                  }
                  if($pr_val == 'L'){
                      $major_product = 2;
                      $TK_web_site = 'https://major-express.ru/ (Выбрать сборные грузы)';
                  }

                  if($Counts->Major_date == $current_date && $Counts->Major > 1000){
                      $status = 'За '.$current_date.' было более 500 запросов к ТК '.$transport_company.'. Просьба продолжить завтра';
                      $CSPP = '';
                      $FIO = "";
                      $date_del = "";
                      $date_del = "";
                      }
                      else{

                        if($Counts->Major_date != $current_date){
                            $Counts->Major = 0;
                            $Counts->Major_date = $current_date;
                        }

                        $Counts->Major = $Counts->Major + 1;
                        $Counts->save();
                      

                  $major = htmlspecialchars(file_get_contents('https://www.major-express.ru/trace.aspx?wbnumber='.$nak_id.'&product='.$major_product.'&type=1'));

                  //только для локального сервера, чтобы убрать ошибку SSL
                  //$major = htmlspecialchars(file_get_contents('https://www.major-express.ru/trace.aspx?wbnumber='.$nak_id.'&product='.$major_product.'&type=1', false, stream_context_create($arrContextOptions)));


                  $pos_major = strripos($major, 'Груз доставлен');

                  if ($pos_major === false) {
                        $status = "В пути";
                        $FIO = "";
                        $date_del = "";
                        $CSPP = 'Груз отправлен, статус отправления: '.$status.', Накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;
                        $array_delivery[$val] = $status.";".$FIO.";".$date_del.";".$transport_company.";".$CSPP;//Добавлем данные по накладной в массив
                    }
                    else{
                        $status = "Отправление доставлено";

                        $major_fio = mb_substr(mb_strstr($major,'Кем получен'),238);
                        $major_fio_pos = strpos($major_fio, '&');
                        $major_fio = trim(substr($major_fio, 0, $major_fio_pos-1));
                        $major_fio = mb_convert_case($major_fio, MB_CASE_TITLE, "UTF-8");

                        $FIO = $major_fio;

                        $major_date_delivery = mb_substr(mb_strstr($major,'Груз доставлен'),241);
                        $major_date_delivery_pos = strpos($major_date_delivery, '/span');
                        $major_date_delivery = trim(substr($major_date_delivery, 0, $major_date_delivery_pos-10));

                        $date_del = $major_date_delivery;
                        
                        $CSPP = 'Груз доставлен, накладная: '.$nak_id.', Получатель: '.$FIO.', Дата доставки: '.$date_del;

                        $array_delivery[$val] = $status.";".$FIO.";".$date_del.";".$transport_company.";".$CSPP;//Добавлем данные по накладной в массив

                    }
                  }
                }
                elseif($pr_val == 'R' && strlen($val) == 11){
                  //Если ТК DPD
                  $status = "";
                  $FIO = "";
                  $date_del = "";
                  $transport_company = "DPD";
                  $TK_web_site = 'https://www.dpd.ru/dpd/home.do2';
                  $CSPP = 'Груз отправлен, накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;
                }
                elseif($pr_val == '4' && strlen($val) == 13){
                  //Если ТК КСЕ
                  $status = "";
                  $FIO = "";
                  $date_del = "";
                  $transport_company = "КСЕ";
                  $TK_web_site = 'https://www.cse.ru/';
                  $CSPP = 'Груз отправлен, накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;
                }
                elseif($pr_val == '2' && (strlen($val) == 13 || strlen($val) == 14)){
                  $nak_id = str_replace("-", "", $nak_id);
                  $buslines = htmlspecialchars(file_get_contents('https://www.dellin.ru/tracker/orders/'.$nak_id.'/'));
                  $pos_buslines = strripos($buslines, 'Заказ завершен');

                  if($pos_buslines === false){
                    $status = 'В пути';
                    $FIO = "";
                    $date_del = "";
                    $transport_company = "Деловые Линии";
                    $TK_web_site = 'https://www.dellin.ru/';
                    $CSPP = 'Груз отправлен, накладная: '.$nak_id.', Сайт ТК для отслеживания: '.$TK_web_site;
                  }
                  else{
                    $status = "Отправление доставлено";
                    
                    $buslines_date = mb_substr(mb_strstr($buslines,'Заказ завершен'),15);
                    $buslines_date_pos = strpos($buslines_date, 'block_paragraph');
                    $buslines_date = trim(substr($buslines_date, 0, $buslines_date_pos-27));

                    $buslines_adress = mb_substr(mb_strstr($buslines,'до адреса'),10);
                    $buslines_adress_pos = strpos($buslines_adress, 'doc-transfer__h3');
                    $buslines_adress = trim(substr($buslines_adress, 0, $buslines_adress_pos-55));

                    $FIO = $buslines_adress;
                    $date_del = $buslines_date;
                    $transport_company = "Деловые Линии";
                    $TK_web_site = 'https://www.dellin.ru/';
                    $CSPP = 'Груз доставлен, накладная: '.$nak_id.', ТК: Деловые Линии, Дата доставки: '.$date_del;
                  }
                }
                else{
                  $status = "Накладная не соответствует Dimex, Major Express, DPD, КСЕ";
                  $FIO = "";
                  $date_del = "";
                  $transport_company = "";
                  $CSPP = "";
                }
                
                }


                

                $report .= '<td> ' . $val . '</td>';
              } /////
            
            }

            elseif($col == 4){
              $report .= '<td> ' . $status . '</td>';
            }
            elseif($col == 5){
              $report .= '<td> ' . $FIO . '</td>';
            }
            elseif($col == 6){
              $report .= '<td> ' . $date_del . '</td>';
            }
            elseif($col == 7){
              $report .= '<td> ' . $CSPP . '</td>';
            }
            /*elseif($col == 8){
              $report .= '<td> ' . $CSPP . '</td>';
            }*/
            else{
              $report .= '<td> ' . $val . '</td>';
            }
        }
        $report .= '</tr>';
    }
    $report .= '</table></div>';
}




    return $report; // метод возвращает значение переменной $report
    } else {
      return false;
    }
  }
 
  public function upload()
  {
    if ($this->validate()) {
      //$dir = 'uploads/'; // Директория - должна быть создана
      //$name = $this->randomFileName($this->file->extension);
      //$file = $dir . $name;
      //$this->file->saveAs($file); // Сохраняем файл
     

      return true;
    } else {
      return false;
    }
  }
 
  private function randomFileName($extension = false)
  {
   /* $extension = $extension ? '.' . $extension : '';
    do {
      $name = md5(microtime() . rand(0, 1000));
      $file = $name . $extension;
    } while (file_exists($file));
    return $file;*/
  }
}
