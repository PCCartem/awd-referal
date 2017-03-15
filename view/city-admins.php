<style>
    .tabs__content {
        display: none; /* по умолчанию прячем все блоки */
    }
    .tabs__content.active {
        display: block; /* по умолчанию показываем нужный блок */
    }
    .tabs__caption li {
        display: inline-block;
        padding: 9px 15px;
        margin: 1px 0 0 1px;
        position: relative;
        border: solid #000;
        border-width: 2px 2px 0;
        border-radius: 5px 5px 0 0;
        text-align: center;
    }
    .tabs__caption li:not(.active) {
        cursor: pointer;
    }
    .tabs__caption li:not(.active):hover {
        background: #eeeeee;
    }
</style>
<div>
    <h2>Админы городов</h2>
        <div class="admins-list">
            <table>
                <thead>
                    <th>Имя пользователя</th>
                    <th>Город</th>
                </thead>
                <tbody>
                <?php
                    foreach ($admins as $admin) {
                ?>
                        <tr>
                            <td><?= $admin['name'] ?></td>
                            <td>
                                <select id-city-admin="<?= $admin['id'] ?>" name="status-lead" class="cityAdminChanger">
                                    <option value="">Нет города</option>
                                    <option <?= $admin['city'] == "Хабаровск" ? "selected " : '' ?>value="Хабаровск">Хабаровск</option>
                                    <option <?= $admin['city'] == "Магнитогорск" ? "selected " : '' ?>value="Магнитогорск">Магнитогорск</option>
                                    <option <?= $admin['city'] == "Череповец" ? "selected " : '' ?>value="Череповец">Череповец</option>
                                </select>
                            </td>
                            <td>
                                <button  id-city-admin="<?= $admin['id'] ?>" class="delCityAdmin" type="button">Удалить из админов</button>
                            </td>
                        </tr>
                <?php
                    }
                ?>

                </tbody>
            </table>
        </div>
    <h2>Партнеры</h2>
    <div class="partner-list">
        <table>
            <thead>
            <th>Имя пользователя</th>
            <th>Город</th>
            <th>Действия</th>
            </thead>
            <tbody>
            <?php
            foreach ($partners as $partner) {
                ?>
                <tr>
                    <td><?= $partner['name'] ?></td>
                    <td>

                        <select id-partner="<?= $partner['id'] ?>" name="city" id="cityRel">
                            <option value="">Нет города</option>
                            <option <?= $partner['city'] == "Хабаровск" ? "selected " : '' ?>value="Хабаровск">Хабаровск</option>
                            <option <?= $partner['city'] == "Магнитогорск" ? "selected " : '' ?>value="Магнитогорск">Магнитогорск</option>
                            <option <?= $partner['city'] == "Череповец" ? "selected " : '' ?>value="Череповец">Череповец</option>
                        </select>
                    </td>
                    <td>
                        <button  id-partner="<?= $partner['id'] ?>" class="addCityAdmin" type="button">Назначить админом города</button>
                    </td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>

</div>
<script type="text/javascript">
    (function($) {
        $(function() {
            $('.cityAdminChanger').on('change', function() {
                var id = $(this).attr('id-city-admin');
                var city = $('select[id-city-admin="'+id+'"]').val();
                var data = {
                    action: 'AwdChangeCityAdmin',
                    'id': id,
                    'city': city
                };

                // с версии 2.8 'ajaxurl' всегда определен в админке
                jQuery.post( '/wp-admin/admin-ajax.php', data, function (d) {
                    alert("Город для админа изменен");
                });
            });

            $('.addCityAdmin').on('click', function() {
                var id = $(this).attr('id-partner');
                var city = $('select[id-partner="'+id+'"]').val();
                var data = {
                    action: 'AwdAddCityAdmin',
                    'id': id,
                    'city': city
                };

                // с версии 2.8 'ajaxurl' всегда определен в админке
                jQuery.post( '/wp-admin/admin-ajax.php', data, function (d) {
                    alert('Админ добавлен');
                });
                location.reload();
            });

            $('.delCityAdmin').on('click', function() {
                var id = $(this).attr('id-city-admin');
                var data = {
                    action: 'AwdDelCityAdmin',
                    'id': id
                };

                // с версии 2.8 'ajaxurl' всегда определен в админке
                jQuery.post( '/wp-admin/admin-ajax.php', data, function () {
                    alert('Админ удален.');
                });
                location.reload();
            });

        });
    })(jQuery);
</script>