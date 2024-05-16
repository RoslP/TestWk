document.addEventListener("DOMContentLoaded", () => {
    let AddData = document.getElementById("write-data-from-categories");
    let DeleteData = document.getElementById("delete-data-from-categories");
    let PutData = document.getElementById("put-data-in-txt");
    let VisualData = document.getElementById("read-data-from-txt");
    let VisualSemiData = document.getElementById("read-data-from-txt-semi");
    AddData.addEventListener("click", function () {

        let jsonData = {
            action: 'InsertData',
            playload: {}

        }

        // Загрузка JSON файла с сервера
        fetch('/Assets/Json/categories.json')
            .then(response => response.json()) // Преобразование ответа в JSON формат
            .then(data => {
                // Запись данных в переменную jsonData
                jsonData.playload = data;

                // Используйте переменную jsonData для работы с данными
                console.log('jsonData:', jsonData.playload);
                $.ajax({
                    url: 'DB/Calls.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(jsonData),
                    success: function (response) {
                        try {
                            JSON.parse(response);
                            alert("Даные уже и так добавлены");
                        } catch (error) {
                            alert("Даные успешно добавлены");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        // Обработка ошибки AJAX
                        console.error('AJAX error:', textStatus, errorThrown);
                    }

                });
            });

    })
    DeleteData.addEventListener("click", function () {
        $.ajax({
            url: 'DB/Calls.php',
            type: 'POST',
            data: JSON.stringify({action: 'DeleteDataFomTable'}),
            success: function () {
                alert('Данные удалены из таблицы')
            }
        })
    })
    PutData.addEventListener("click", function () {
        $.ajax({
            url: 'DB/Calls.php',
            type: 'POST',
            data: JSON.stringify({action: 'PutDataFomTable'}),
            success: function () {
            alert('Записанно');
            }
        })
    })
    VisualData.addEventListener("click", function () {
        ajaxCreater('Assets/Txt/compleate.txt')
    });
    VisualSemiData.addEventListener("click", function () {
        ajaxCreater('Assets/Txt/onliName.txt')
    })

    function ajaxCreater(url) {
        $('.Export').empty()
        $.ajax({
            url: url,
            dataType: 'text',
            success: function(data) {
                // Разделяем строки по символу новой строки
                let lines = data.split('\n');

                // Создаем блок <label> для вывода
                let label = $('<label>').addClass('Elll LABLE221');

                // Проходимся по каждой строке и добавляем ее в блок <label>
                lines.forEach(function(line) {
                    // Считаем количество символов табуляции в начале строки
                    let tabCount = 0;
                    while (line.charAt(tabCount) === '\t') {
                        tabCount++;
                    }

                    // Создаем отступы для сохранения табуляции
                    let indentation = '';
                    for (var i = 0; i < tabCount; i++) {
                        indentation += '&nbsp;&nbsp;&nbsp;&nbsp;'; // Вставляем HTML-код пробела (для отображения отступа)
                    }

                    // Создаем элемент <div> для каждой строки и добавляем его в блок <label>
                    let div = $('<div>').html(indentation + line.trim()); // Удаляем лишние пробелы и добавляем отступы
                    label.append(div);
                });

                // Добавляем блок <label> на страницу
                $('.Export').append(label);
            },
        });
    }

})