<style>
    a:focus, a:hover{
        color: #FFF !important;
    }
    .tabs__content {
        display: none; /* по умолчанию прячем все блоки */
    }
    .tabs__content.active {
        margin: 30px 0;
        display: block; /* по умолчанию показываем нужный блок */
    }
    .tabs__caption li {
        display: inline-block;
        padding: 9px 15px;
        margin: 1px 0 0 1px;
        position: relative;
        text-align: center;
    }
    .tabs__caption li.active {
        background: #eeeeee;
    }
    .tabs__caption li:not(.active) {
        cursor: pointer;
    }
    .tabs__caption li:not(.active):hover {
        background: #eeeeee;
    }
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"></script>
<div class="tabs">
    <a href="/" class="btn btn-default" type="button">Призывникам</a>
    <ul class="tabs__caption nav nav-tabs">
        <li class="active">Профиль</li>
        <li>Список лидов</li>
    </ul>
    <div class="tabs__content admin-panel active">
        <div class="container">
            <div class="user-info col-sm-8">
            <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                <div class="form-group">
                    <label for="user-email">E-mail</label>
                    <input class="form-control" name="email"  value="<?= $user->email ?>" placeholder="Введите ваш Email" type="text">
                </div>
                <div class="form-group">
                    <label for="user-name">Имя пользователя</label>
                    <input class="form-control" name="username" value="<?= $user->name ?>" placeholder="Введите ваше имя" type="text">
                </div>
                <div class="form-group">
                    <label for="user-phone">Номер телефона</label>
                    <input class="form-control" name="phone"  value="<?= $user->number ?>" placeholder="Введите ваш номер" type="text">
                </div>
                <div class="form-group">
                    <label for="user-type-account">Тип счета</label>
                    <select class="form-control" name="type-account" id="user-type-account">
                        <option value="">Тип вашего счета</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user-number-account">Номер счета</label>
                    <input class="form-control" name="number-account" value="<?= $user->numberAccount ?>" placeholder="Введите номер счета" type="text">
                </div>
                <input type="hidden" name="action" value="AwdUpdateUser">
                <button class="btn btn-success">Изменить данные</button>
            </form>
        </div>
            <div class="control-panel col-sm-4">
                <div class="user-statistic text-center">
                    <div>Лидов: <?= count($leads) ?></div>
                    <div>Продаж: <?= count($paidLeads) ?></div>
                    <div>Конверсия: <?= count($paidLeads)>0? (count($paidLeads)/round(count($leads), 2))*100 : 0 ?>%</div>
                </div>
                <div class="user-balance  text-center">
                    Баланс: <?= $user->balance ?> руб.
                </div>
                <div class="form-group">
                    <button id="sendOderForGetPaid" class="btn btn-default form-control" type="button">Запрос на вывод средств</button>
                </div>
                <div class="form-group">
                    <a href="/trenings" class="btn btn-default form-control" type="button">Обучение</a>
                </div>

            </div>
        </div>
    </div>
    <div class="tabs__content">
        <div class="form-group referal-link">
            <label for="user-referal-link">Ссылка</label>
            <input class="form-control" id="user-referal-link" value="<?= $user->referalLinc ?>" type="text">
            <br>
            <button id="copyButton" class="btn btn-default" data-clipboard-target="#user-referal-link" type="button">Скопировать</button>
        </div>
        <div class="lead-list">
            <table class="table">
                <thead>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Город</th>
                    <th>Статус</th>
                </thead>
                <tbody>
                <?php
                    foreach ($leads as $lead) {
                ?>
                        <tr>
                            <td><?= $lead['name'] ?></td>
                            <td><?= $lead['number'] ?></td>
                            <td><?= $lead['city'] ?></td>
                            <td>
                                <?= $lead['status'] == 1 ? "Новый" : '' ?>
                                <?= $lead['status'] == 2 ? "Сделка" : '' ?>
                                <?= $lead['status'] == 3 ? "Консультация" : '' ?>
                                <?= $lead['status'] == 4 ? "Оплачено" : '' ?>
                            </td>
                        </tr>
                <?php
                    }
                ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    (function($) {
        $(function() {
            new Clipboard('#copyButton');
            $('ul.tabs__caption').on('click', 'li:not(.active)', function() {
                $(this)
                    .addClass('active').siblings().removeClass('active')
                    .closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
            });

        });
    })(jQuery);
    jQuery(document).ready(function() {
        new Clipboard('#copyButton');
        $('#sendOderForGetPaid').on('click', function() {
            var data = {
                action: 'AwdGetMePaid'
            };

            // с версии 2.8 'ajaxurl' всегда определен в админке
            jQuery.post( '/wp-admin/admin-ajax.php', data, function () {
                alert('Запрос отправлен');
            });

        });
    });
</script>