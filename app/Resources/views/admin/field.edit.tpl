<h4>Свойства</h4>
            <table style="width:350px;margin-left:15px" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td>Название:<br>
                  <input type="text" name="title" class="field-props" value="{$a.title}" /></td>
              </tr>
              <tr>
                <td>Системное имя:<br>
                  <input type="text" name="name" class="field-props" value="{$a.name}" /></td>
              </tr>
              <tr>
                <td>Сортировка:<br>
                  <input type="text" name="order_by" class="field-props" value="{$a.order_by}" /></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                      <td><input type="checkbox" name="is_lang" {if $a.is_lang}checked{/if} value="on" />
                        многоязычная</td>
                      <td><input type="checkbox" name="no_insert" {if $a.no_insert}checked{/if} value="on" />
                        запрет добавления</td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" name="is_sort" {if $a.is_sort}checked{/if} value="on" />
                        с полем сортировки</td>
                      <td><input type="checkbox" name="no_update" {if $a.no_update}checked{/if} value="on" />
                        запрет редактирования</td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" name="is_publish" {if $a.is_publish}checked{/if} value="on" />
                        c полем публикации</td>
                      <td><input type="checkbox" name="no_delete" {if $a.no_delete}checked{/if} value="on" />
                        запрет удаления</td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" name="is_view" {if $a.is_view}checked{/if} value="on" />
                        вид "дерево"</td>
                      <td><input type="checkbox" name="is_search" {if $a.is_search}checked{/if} value="on" />
                        поисковое поле</td>
                    </tr>
                  </table></td>
              </tr>
              <tr>
                <td>Префикс поиска:<br>
                  <input type="text" name="search_prefix" class="field-props-short" value="{$a.search_prefix}" /></td>
              </tr>
              <tr>
                <td>Сорт.:<br>
                  <input type="text" name="ord" class="field-props-short" value="{$a.ord}" /></td>
              </tr>
              <tr>
                <td>Активный:<br>
                  <input type="checkbox" name="publish" {if $a.publish}checked{/if} value="on" /></td>
              </tr>
            </table>