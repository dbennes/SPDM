<?php
if(isset($_POST["submit"])) {
    $file = $_FILES['file']['tmp_name'];
    require_once 'Classes/PHPExcel.php';

    $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
    $excelObj = $excelReader->load($file);
    $worksheet = $excelObj->getSheet(0);
    $lastRow = $worksheet->getHighestRow();
    $lastColumn = $worksheet->getHighestColumn();

    echo "<table border='1'>";
    for ($row = 1; $row <= $lastRow; $row++) {
        echo "<tr>";
        echo "<td>".$worksheet->getCell('A'.$row)->getValue()."</td>";
        echo "<td>".$worksheet->getCell('B'.$row)->getValue()."</td>";
        echo "<td><button onclick='myFunction()'>Button</button></td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>