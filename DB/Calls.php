<?php
require 'Connect.php';

class Calls
{
    private $pdo;
    public static $numberOfChild = 0;
    static public $Message = '';

    public function __construct()
    {
        $this->pdo = Connect::getConnection();
    }

    //Удаление данных из таблицы
    public function DeleteFromHeader()
    {
        $sql = "DELETE FROM header";
        $this->pdo->exec($sql);
    }

    //Если таблица не пустаня, происходит запись в нее из categories.json
    public function DataIsEmpty($data): int
    {
        $sql = "SELECT * FROM header WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $data[0]['id']);
        $stmt->execute();
        $result = $stmt->fetch(); // Получаем одну строку результата
        if (!$result) {
            $this->addDataToDb($data, Calls::$numberOfChild);
            return 0;
        } else {
            return 1;
        }
    }

    //Добавляет данные из categories.json в Базу данных.
    public function addDataToDb($data, &$childNumber, $fatherId = ''): void
    {
        //колличесвто итераций для обхода внешних массивов
        $external = 0;
        while ($external < count($data)) {
            $keys = '';
            $masks = '';
            $isFirst = true;
            foreach ($data[$external] as $key => $value) {

                //если попадаем на ребенка то номер вложенности $childNumber увеличивается
                //вызываем рекурсию по новой
                if (is_array($value)) {
                    $childNumber++;
                    $this->addDataToDb($value, $childNumber, $data[$external]['id']);
                }
                //если не первое значение и не массив в виде ребенка
                if (!is_array($value) && !$isFirst) {
                    //если числовое значение
                    if (is_numeric($value)) {
                        $keys .= ", $key";
                        $masks .= ", $value";
                    }
                    //если не числовое
                    if (!is_numeric($value)) {
                        $keys .= ", $key";
                        $masks .= ", '$value'";
                        //если текущий ключ массива является строкой "alias" и нету вложенности (главный родительский элемент)
                        if ($key === "alias" && $fatherId === '') {
                            $sql = "INSERT INTO header ($keys, childrens) VALUES($masks, $childNumber)";
                            $this->pdo->exec($sql);
                        } //если текущий ключ массива является строкой "alias" и он дочерний элемент
                        elseif ($key === "alias" && $fatherId !== '') {
                            $sql = "INSERT INTO header ($keys, childrens, father_Id) VALUES($masks, $childNumber, $fatherId)";
                            $this->pdo->exec($sql);
                        }
                    }

                }
                //если первое значение
                if (is_numeric($value) && $isFirst) {
                    $keys .= "$key";
                    $masks .= $value;
                    $isFirst = false;
                }
            }
            echo $keys;
            echo $masks;
            $external++;
        }
        //если цикл отработал для всех вложенных массивов и не зашел в рекурсию
        //уменьшаем уровень вложенности
        $childNumber--;
    }

    //Выводит данные из базы данных как говорится в ТЗ

    // Перый параметр - уровень вложенности для текущего элемента.
    // Второй - id родителя,
    // Третий - массив всех данных,
    // Тетвертый - текущая строка, которая формируется для записи,
    // Пятый - максимальная вложенность.
    public function getDataToHeader($numberOfChild, $fatherId = '', $allData = [], $currentChain = '', $anotherCounter = 0, $resultArray=[])
    {
        $sql = "SELECT * FROM header";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents('../Assets/Json/all_data.json', json_encode($result));
        if ($fatherId === '') {


            $jsonData = file_get_contents('../Assets/Json/all_data.json');
            $allData = json_decode($jsonData, true);
            $semiCounter = 0;
            $arrayOfAllElements = [];
            file_put_contents('../Assets/Txt/data.txt', '');
            file_put_contents('../Assets/Txt/compleate.txt',"");
            file_put_contents('../Assets/Txt/onliName.txt',"");
            //Нахождение максимального уровня вложенности
            foreach ($allData as $key => $value) {
                if (intval($value['childrens']) > $numberOfChild) {
                    $numberOfChild = intval($value['childrens']);
                }
            }
            //Далее заполняю массив колличеством вложенностей
            while ($semiCounter <= $numberOfChild) {
                foreach ($allData as $key => $value) {
                    if (intval($value['childrens']) === $semiCounter) {
                        $arrayOfAllElements[$semiCounter] += 1;
                    }
                }
                $semiCounter++;
            }
            $anotherCounter = count($arrayOfAllElements);
        }


        if ($currentChain === '') {
            //пока последний тип вложенности не будет 0 (не станет главным родителем). Может быть N-типов вложенностей и кажый дра значение будет браться от
            //N-колличества вложенностей в  $anotherCounter = count($arrayOfAllElements);
            //Сначала добавляются элементы с максимальной вложенностью. Последними добавляются элементы с минимальной т.е. (0) вложенностью.
            while ($anotherCounter >= 0) {
                foreach ($allData as $key => $value) {
                    //если ребенок равен текущему уровню вложенности. И номер вложенности не меньше нуля.
                    if (intval($value['childrens']) === $numberOfChild && $numberOfChild >= 0) {
                        $currentChain = " ";
                        $currentChain .= "${value['name']} /${value['alias']}";
                        $this->getDataToHeader($numberOfChild, $value['father_Id'], $allData, $currentChain);
                    }
                }
                $anotherCounter--;
                $numberOfChild--;
            }
        }
        //если уже сделал присваивание значения в строку/ второе условие чтобы не заходило повторно под конец, при конце всех рекурсий.
        if ($currentChain !== '' && $numberOfChild >= 0) {
            $numberOfChild--;
            //Выполняется пока уровень вложенности не меньше нуля
            if ($numberOfChild >= 0) {
                foreach ($allData as $key => $value) {
                    if ($value['id'] === $fatherId) {
                        //Далее реурсивный вызов по родительскому id и добавление "alias" в начало строки.
                        $currentChain = preg_replace('/(\s\/)/', " /{$value['alias']}/", $currentChain);
                        $this->getDataToHeader($numberOfChild, $value['father_Id'], $allData, $currentChain);
                    }
                }
            }
            if ($numberOfChild < 0) {
                //флаг FILE_APPEND - указывает что данные нужно добавить в конец файла а не перезаписать его
                file_put_contents('../Assets/Txt/data.txt', $currentChain . "\n", FILE_APPEND);
            }
        }
        //Если все итерации цикла выполнены и записи в .txt файл произведены
        if ($anotherCounter < 0) {
            $lines = file("../Assets/Txt/data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $arrayLet = [];
            $arraiI = 0;

            //Далее получение списка основных категорий
            foreach ($lines as $line) {

                if (preg_match('/^[^\/]*\/[^\/]*$/', $line)) {
                    //достаю название категории после '/'
                    $Category = explode('/', $line);
                    //далее обращаюсь ко вторй [0,1] часте строке содержащей название категории
                    array_push($arrayLet, trim($Category[1]));

                }

            }
            $iter = 0;
            while ($iter < count($arrayLet)) {
                //создаю временный массив чтобы после форича в новой итерации вайл его сбросить.
                $timeLessArray = [];
                foreach ($lines as $line) {
                    //сбор данных по одной конкретной категории.
                    if (strpos($line, $arrayLet[$iter])) {
                        array_push($timeLessArray, $line);
                    }
                }

                $arrayParts = [];
                //получение второй категории
                foreach ($timeLessArray as $line) {
                    $parts = explode('/', $line);
                    if (!in_array(trim($parts[2]), $arrayParts) && trim($parts[2] !== null)) {
                        array_push($arrayParts, trim($parts[2]));
                    }

                }
                //Запись первой строки(главной)
                $correctArrayOfResult = [];
                foreach ($timeLessArray as $line) {
                    $pos = strpos($line, "/$arrayLet[$iter]");
                    if ($pos !== false && substr($line, $pos + strlen("/$arrayLet[$iter]/")) === '') {
                        $correctArrayOfResult[] = $line;
                    }
                }
                $counterParts = count($arrayParts);
                $counter = 0;
                while ($counterParts > 0) {
                    foreach ($timeLessArray as $line) {
                        $pos = strpos($line, "/$arrayParts[$counter]");
                        //если это второй слеш к примеру после /users/list и после него ничего нет
                        if ($pos !== false && substr($line, $pos + strlen("/$arrayParts[$counter]/")) === '') {
                            $correctArrayOfResult[] = "\t" . $line;
                            foreach ($timeLessArray as $line2) {
                                $pathComponents = explode('/', $line2);
                                $lastPathComponent = end($pathComponents);
                                if ($lastPathComponent !== $arrayParts[$counter] && strpos($line2, "$arrayParts[$counter]")) {
                                    $correctArrayOfResult[] = "\t\t" . $line2;
                                }
                            }
                        }
                    }
                    $counter++;
                    $counterParts--;
                }
                $resultArray=array_merge($resultArray,$correctArrayOfResult);
                $iter++;
            }

            $content = implode(PHP_EOL, $resultArray);
            file_put_contents('../Assets/Txt/compleate.txt',$content, FILE_APPEND);

            //Далее помещаю массив не далее первого уровня вложенности
            foreach ($resultArray as $key=>$value)
            {
                $tabCount = substr_count($value,"\t");
                if ($tabCount>1)
                {
                    unset ($resultArray[$key]);
                }
            }

            foreach ($resultArray as $key => $value) {
                // Находим позицию символа "/" в строке
                $pos = strpos($value, '/');
                if ($pos !== false) { // Если символ "/" найден
                    // Обрезаем строку до символа "/"
                    $resultArray[$key] = substr($value, 0, $pos);
                }
            }

            $content = implode(PHP_EOL, $resultArray);
            file_put_contents('../Assets/Txt/onliName.txt',$content, FILE_APPEND);
        }
    }
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['action'])) {
    if ($data['action'] === 'InsertData') {
        $result = (new Calls())->DataIsEmpty($data['playload']);
        //Далее передаю возвращенное значение функцие обратно в Ajax
        echo json_decode($result);
    }
    if ($data['action'] === 'DeleteDataFomTable') {
        (new Calls())->DeleteFromHeader();
    }
    if ($data['action'] === 'PutDataFomTable') {
        (new Calls())->getDataToHeader(Calls::$numberOfChild);
    }
}