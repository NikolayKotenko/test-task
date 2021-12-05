# test-x-page

1. Развернуть битрикс старт на хостинге на ваш выбор.
2. Создать инфоблок и наполнить его случайными элементами с картинкой. детальной картинкой, превью и детальным описанием. Штук 10-20.
3. Вывести комплексный компонент новостей/каталога с выводом списка элементов и возможностью посещения детальной страницы.
4. Создать хайлоадблок, суть которого собирать информацию о посещении пользователем элемента. обязательно должны быть поля: датавремя посещения, ip адрес, идентификатор/код посещенного элемента
5. при посещении элемента записывать этот визит в хайлоадблок для ведения статистики
6. Наполнить хайлоадблок тестовыми данными, имитирующие 10 000 000 посещений элементов равномерно в течение года. (случайный разброс по элементам, дням, часам, минутам). этот шаг необходим для оценки эффективности работы программиста далее.

7. Ввывести в карточке элемента статистику(график/таблица) в любом виде (любая библиотека графиков или просто распечатать читаемо таблицей) в которой  отражено:
- распределение посещений за сегодня по часам. (например 12 - 12313, 13 -34224, 14 -132, 15 - 780)
- распределение посещений этого элемента по дням за последний месяц (например 04.08.2021 - 12132, 05.08.2021 -17)