<?php
/**
 * ### Доступные переменные:
 *
 * $original        путь к большому изображению
 * $thumbs          путь к миниатюре
 *
 * $album['id']     ID альбома
 * $album['w']      ширина изображения
 * $album['h']      высота изображения
 *
 * $item['name']    имя изображения
 * $item['title']   заголовок изображения
 *
 * ### Дополнительные поля
 *
 * Пример вывода доп. поля с ключом link
 * [php] if (!empty($item['link'])) echo $item['link']; [/php]
 *
 * ### Вывод всех доп. полей
 *
 * [php]
 *  if (count($fields) > 0) {
 *      foreach ($fields as $field) {
 *          if (!empty($item[$field['slug']])) {
 *              echo $field['name'] . ': ' . $item[$field['slug']] . '<br>';
 *          }
 *      }
 *  }
 * [/php]
 */
 
echo '<ul class="stock">';

foreach ($files as $item) {
    echo "<li>";
    echo "<a href='{$original}{$item['name']}' rel='stock{$album['id']}' title='{$item['title']}'><img src='{$thumbs}{$item['name']}' alt='{$item['title']}'></a><br>";
    if (count($fields) > 0) {
        foreach ($fields as $field) {
            if (!empty($item[$field['slug']])) {
                echo $field['name'] . ': ' . $item[$field['slug']] . '<br>';
            }
        }
    }
    echo "</li>";
}

echo '</ul>';