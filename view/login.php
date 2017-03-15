

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
<div class="tabs">
    <a href="/" class="btn btn-default" type="button">Призывникам</a>
    <ul class="tabs__caption nav nav-tabs">
        <li class="active">Войти</li>
        <li>Регистрация</li>
    </ul>

    <div class="admin-panel">
        <div class="tabs__content active">
            <div>
                <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" id="loginForm" method="post">
                    <div class="form-group field">
                        <label for="login">Имя пользователя:</label>
                        <input class="form-control"  required type="text" name="log" value="" id="login">
                    </div>
                    <div class="form-group field">
                        <label for="pass">Пароль:</label>
                        <input class="form-control" required type="password" name="pwd" value="" id="pass">
                    </div>
                    <div class="form-group rememberme">
                        <input name="rememberme" type="checkbox" id="rememberme" value="forever"> <label for="rememberme">Запомнить меня</label>
                    </div>
                    <input type="hidden" name="action" value="AwdLogin">
                    <div class="form-group submit">
                        <input class="btn btn-success" name="submit" type="submit" value="Войти">
                    </div>

                </form>
            </div>

        </div>
        <div>
            <div class="tabs__content">
                <div>
                    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" id="m_reg_user_form">
                        <div class="form-group">
                            <label for="m_user_name">Имя:</label>
                            <input class="form-control" required type="text" name="name" id="m_user_name">
                        </div>
                        <div class="form-group">
                            <label for="m_user_email\">Эл. почта:</label>
                            <input class="form-control" required type="email" name="email" id="m_user_email">
                        </div>
                        <div class="form-group">
                            <label for="m_user_pass\">Пароль:</label>
                            <input class="form-control" required type="password" name="pass" id="m_user_pass">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="action" value="AwdRegister">
                            <input class="btn btn-success" type="submit" name="m_regiser_usr_btn" value="Зарегистрироваться">
                        </div>
                    </form>
                </div>
                </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $(function() {

            $('ul.tabs__caption').on('click', 'li:not(.active)', function() {
                $(this)
                    .addClass('active').siblings().removeClass('active')
                    .closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
            });

        });
    })(jQuery);
    jQuery(document).ready(function($) {
        $('option[value="4"]:selected').parent().parent().html('Оплачено');
        $('#status-lead').on('change', function() {
            var data = {
                action: 'AwdUpdateStatus',
                'lead-id':  $(this).attr('id-lead'),
                'status': $(this).val()
            };

            // с версии 2.8 'ajaxurl' всегда определен в админке
            jQuery.post( '/wp-admin/admin-ajax.php', data);
        });


        $('.clouseLead').on('click', function() {
            var lid = $(this).attr('id-lead');
            var data = {
                action: 'AwdCloseLead',
                'lead-id': lid
            };

            // с версии 2.8 'ajaxurl' всегда определен в админке
            jQuery.post( '/wp-admin/admin-ajax.php', data);

            $('select[id-lead="'+$(this).attr('id-lead')+'"]').parent().html('Оплачено');
        });
    });
</script>