<div class="wrap">
    <h1>Ustawienia kalendarza</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'product_datepicker_settings_group' ); ?>
        <?php do_settings_sections( 'product_datepicker_settings_group' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">ID produktów (jeśli chcesz używać w WooCommerce)</th>
                <td>
                    <input type="text" name="product_datepicker_product_ids" style="font-family: monospace; min-width: 500px" value="<?php echo esc_attr( get_option('product_datepicker_product_ids', null) ); ?>">
                    <p class="description">
                        Podaj ID produktu, do którego ma być przypisany widget daty. Kolejne wartości oddzielaj przecinkami.<br>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">ID WPForms (jeśli chcesz używać przy WPForms)</th>
                <td>
                    <div>
                        <input type="number" name="product_datepicker_wp_forms_id_form" style="font-family: monospace; min-width: 500px" value="<?php echo esc_attr( get_option('product_datepicker_wp_forms_id_form', null) ); ?>">
                        <p class="description">
                            Podaj ID formularza.<br>
                        </p>
                    </div>
                    <div>
                        <input type="number" name="product_datepicker_wp_forms_id_field" style="font-family: monospace; min-width: 500px" value="<?php echo esc_attr( get_option('product_datepicker_wp_forms_id_field', null) ); ?>">
                        <p class="description">
                            Podaj ID pola, które zostanie zastąpione polem z datą w formularzu.<br>
                        </p>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">ID Forminator (jeśli chcesz używać przy Forminatorze)</th>
                <td>
                    <div>
                        <input type="number" name="product_datepicker_forminator_id_form" style="font-family: monospace; min-width: 500px" value="<?php echo esc_attr( get_option('product_datepicker_forminator_id_form', null) ); ?>">
                        <p class="description">
                            Podaj ID formularza.<br>
                        </p>
                    </div>
                    <div>
                        <input type="text" name="product_datepicker_forminator_id_field" style="font-family: monospace; min-width: 500px" value="<?php echo esc_attr( get_option('product_datepicker_forminator_id_field', null) ); ?>">
                        <p class="description">
                            Podaj ID pola, które zostanie zastąpione polem z datą w formularzu.<br>
                        </p>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Daty wykluczone z kalendarza</th>
                <td>
                    <textarea name="product_datepicker_excluded_days" rows="10" cols="70" style="font-family: monospace;"><?php echo esc_textarea( get_option('product_datepicker_excluded_days', null) ); ?></textarea>
                    <p class="description">
                        Podaj daty w formacie yyyy-mm-dd np. 2026-01-24. Kolejne daty oddzielaj przecinkami.<br>
                        W tym miejscu pojawiać się będą również daty z zamówień - nie usuwaj ich.<br>
                        Przestarzałe daty są usuwane automatycznie.
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>