<?php

if (!function_exists('formDropdownOptgroup')) {

    function formDropdownOptgroup($options_name, $name = '', $options = [], $label = '', $selected_id = null, $has_null = false, $id = null, $is_disabled = false, $is_required = false, $is_multiple = false, $is_hidden = false, $select_extra_class = null, $on_change = null) {
        $selected_id = $selected_id ?? old($name);
        $res = "<div class='form-group' ".($is_hidden ? ' hidden ' : '').">
          <label>$label</label>
          <select name='$name' class='form-control border border-secondary $select_extra_class' id='$id' style='width: 100%;' tabindex='-1' aria-hidden='true' ".($is_multiple ? ' multiple ' : '').($is_disabled ? ' disabled ' : '').($is_required ? ' required ' : '')." onChange='$on_change'>
           ";
        if($has_null) {
            $res .= "
                <option value>Не выбрано</option>
            ";
        }
        foreach ($options ?? [] as $option_group) {
            $option_group_title = $option_group->title ?? $option_group->name;
            $res .= "<optgroup label='$option_group_title'>";
            foreach ($option_group->$options_name ?? [] as $option) {
                if(!isset($option['id'])) {
                    $value = $option;
                    $text = $option;
                } else {
                    $value = $option['id'];
                    $text = $option['name'] ?? $option['title'] ?? $option['body'];
                }
                $is_selected = $is_multiple
                    ? in_array($value, $selected_id ?? [])
                    : ($selected_id == $value);

                $res .= "
                <option ".($is_selected ? 'selected="selected"' : '') . " value='$value'>".__($text)."</option>
                ";
            }
            $res .= "</optgroup>";
        }
        $res .= "
          </select>
        </div>
    ";
        return $res;
    }
}

if (!function_exists('formInput')) {

    function formInput(
        $name = '',
        $placeholder = '',
        $value = null,
        $label = '',
        $is_hidden = false,
        $id = null,
        $is_disabled = false,
        $is_required = false,
        $type = '',
        $step = 'any',
        $is_readonly = false,
        $div_class = null,
        $input_class = null,
        $input_id = null,
        $min = null,
        $max = null,
        $additional_root_class = null,
        $mask = null
    )
//        dd($mask);
    {
        if (!$max) {
//            dump($mask);
            if ($type == 'number') {
                $max = 21474836475;
            } else if ($type == '' || $type == 'text') {
                $max = 255;
            }
        }
        $value = $value ?? old($name);
        return "
            <div class='" . ($div_class ?? "mb-2 col-lg-18 $additional_root_class") . ($is_required ? ' required ' : '') . " ' id='$id'>
                    <label>$label</label>
                    <input
                        " . ($is_hidden ? ' hidden ' : '') .
            ($is_disabled ? ' disabled ' : '') .
            ($is_required ? ' required ' : '') .
            ($is_readonly ? ' readonly ' : '') . "
                        value='$value'
                        type='$type'
                        class='" . ($input_class ?? "form-control  border border-secondary") . "'
                        name='$name'
                        placeholder='$placeholder'
                        step='$step'
                        id='$input_id'
                        min='$min'
                        max='$max'
                        maxlength='$max'
                        data-mask='$mask'
                        aria-describedby='basic-addon1'
                        >
            </div>
        ";
    }
}

if (!function_exists('formFile')) {

    function formFile(
        $name = '',
        $label = '',
        $image = null,
        $is_hidden = false,
        $video = null,
        $footer_label = '',
        $link = '',
        $is_required = false,
        $accept = null,
        $images_delete_route = null,
        $update_order_route_raw = null,
        $is_multiple = false,
        $is_disabled = false,
        $image_el_path_name = 'path'
    )
    {
        $hash = md5(uniqid(mt_rand(10000000, 100000000)));
        $is_multiple = $is_multiple ?: str_ends_with($name, '[]');
        $res = "
            <label style='font-style: italic;padding: 0px 4px'>$label</label>
            <div class='input-group mb-3 " . ($is_required ? ' required ' : '') . "' " . ($is_hidden ? 'hidden' : '') . " >
                <input type='file' class='form-control border border-top-0 border border-right-0 border-left-0 border-secondary' id='inputGroupFile$hash' name='$name' " . ($is_multiple ? ' multiple ' : '') . ($is_required ? ' required ' : '') .($is_disabled ? ' disabled ' : '') . " accept='$accept'>
                <label style='font-style: italic;padding: 0px 4px' for='inputGroupFile$hash' class='input-group-text'>$label</label>
            </div>";
        if ($image) {
            if(in_array(gettype($image), ['array', 'object'])) {
                $res .= "
                    <!-- Carousel wrapper -->
                    <div
                        id='carouselMultiItemExample'
                        class='carousel slide carousel-dark text-center'
                        data-mdb-ride='carousel'
                    >
                        <!-- Inner -->
                        <div class='carousel-inner py-4'>

                            <!-- Single item -->
                            <div class='carousel-item active'>
                                <div class='container'>
                                    <div class='row'>
                                        <button id='gallery_btn_$hash' class='btn btn-info' >". __('update order') ."</button> <!--TODO l11n-->
                                    </div>
                                    <div class='card-subtitle mb-2 text-muted'>
                                        ".(__('drag and replace items and then click update order'))."
                                    </div>
                                    <ul id='gallery#$hash' class='row  list-group-horizontal' >";
                foreach ($image as $image_el) {
                    $res .= "
                        <li class='col-lg-3'>
                            <div class='card'>
                                <img
                                    src='".$image_el[$image_el_path_name]."'
                                    class='card-img-top'
                                    alt='image'
                                    data-id='".$image_el['id']."'
                                />";
                    if($images_delete_route) {
                        $res .= "
                            <div class='card-body'>
                                <button type='button' onclick=\"deleteEventImage(this, '".route($images_delete_route, $image_el['id'])."')\" class='btn btn-primary'>Удалить </button>
                            </div>
                        ";
                    }
                    $res .= "
                        </div>
                    </li>
                    <script>
                    function deleteEventImage(button, delete_url) {
                        $.post(delete_url, {
                            '_token': '" . csrf_token() . "',
                            '_method': 'DELETE'
                        }, function() {
                            $(button).parent().parent().parent().remove()
                        })
                    }
                    </script>
                    ";
                }

                if ($update_order_route_raw) {
                    $res .= "
                        <script>
                            $(document).ready(function() {
                                slist(document.getElementById('gallery#$hash'));
                                $('#gallery_btn_$hash').click(function (e) {
                                    var active = $(e.target).closest('.carousel-item.active');
                                    var activeImages = active.find('img');
                                    var ids = [];
                                    activeImages.each(function() {
                                        ids.push({id: $(this).data('id')});
                                    });
                                    console.log(activeImages)
                                    $.post('".$update_order_route_raw."', {
                                        '_token': '" . csrf_token() . "',
                                        '_method': 'PUT',
                                        'items': ids
                                    });
                                });
                            });
                        </script>
                    ";
                }

                $res .=            "</ul>
                                </div>
                            </div>
                        </div>
                        <!-- Inner -->
                    </div>
                ";
            } else {
                $res .= "
                <div class='form-group border border-secondary'>
                    <div class='row'>
                        <div class='col-3'>
                            <div class='card-body'>
                                <img src='$image' width='50%'>
                            </div>
                            <div class='card-footer'>
                                $footer_label
                            </div>
                        </div>
                    </div>
                </div>
            ";
            }
        }
        if ($video) {
            $res .= "
                <div class='form-group'>
                    <video controls src='$video'></video>
                </div>
            ";
        }
        if ($link) {
            $res .= "
                <div class='form-group'>
                    <a href='$link'>$footer_label</a>
                </div>
            ";
        }

        return $res;
    }
}
if (!function_exists('formTextarea')) {

    function formTextarea($name = '', $placeholder = '', $value = null, $label = '', $is_readonly = false, $rows = 6, $id = null, $input_class = null, $is_required = false)
    {
        $value = $value ?? old($name);
        return "
            <div class='form-group mb-3 '>
                <label>$label</label>
                <textarea name='$name' id='$id' class='". ($input_class ?? 'form-control') ."' rows='$rows' placeholder='$placeholder' ". ($is_readonly ? ' readonly ' : '') . ($is_required ? ' required ' : '') . " >$value</textarea>
            </div>
        ";
    }
}
if (!function_exists('formSubmitButton')) {

    function formSubmitButton($placeholder = '', $button_class = null, $div_class = null, $is_disabled = false)
    {
        return "
            <div class=' " . ($div_class ?? 'mt-4') . " '>
              <button type='submit' class=' " . ($button_class ?? 'btn btn-primary') . " ' ". ($is_disabled ? ' disabled ' : '') ." >$placeholder</button>
            </div>
        ";
    }
}
if (!function_exists('formDropdown')) {

    /**
     * @param string $name
     * @param array $options
     * @param string $label
     * @param null $selected_id
     * @param bool $has_null
     * @param null $id
     * @param bool $is_disabled
     * @param bool $is_required
     * @param bool $is_multiple
     * @param null $select_class
     * @param string $option_text_key
     *  This array required for crud operations.
     *  Update and destroy must contain {id} in route, it will be replaced by id of the selected option
     *
     * @return string
     */
    function formDropdown(
        $name = '',
        $options = [],
        $label = '',
        $selected_id = null,
        $has_null = false,
        $id = null,
        $is_disabled = false,
        $is_required = false,
        $is_multiple = false,
        $select_class = null,
        $option_text_key = 'name',
    )
    {
        $selected_id = $selected_id ?? old($name);
        $res = "
        <div
            class='form-group custom-form-dropdown"
            . ($is_required ? ' required ' : '')
            . "'>
          <label>".__($label)."</label>
            <select name='$name'
                class='"
            . ($select_class ?? 'form-control  border border-secondary custom-form-dropdown-select')
            . "'
                id='$id'
                style='width: 100%;'
                data-select2-id='1'
                tabindex='-1'
                aria-hidden='true' "
            . ($is_multiple ? ' multiple ' : '')
            . ($is_disabled ? ' disabled ' : '')
            . ($is_required ? ' required ' : '')
            . ">";
        $res .= $has_null
            ? "<option value>Не выбрано</option>"
            : "<option value disabled>Выберите вариант</option>";
        foreach ($options as $option) {
            if (!isset($option['id'])) {
                $value = $option;
                $text = $option;
            } else {
                $value = $option['id'];
                $text = $option[$option_text_key] ?? $option['title'];
            }
            $is_selected = $is_multiple
                ? in_array($value, $selected_id ?? [])
                : ($selected_id == $value);

            $res .= "
                <option "
                . ($is_selected ? 'selected="selected"' : '')
                . " value='$value'>"
                . __($text)
                . "</option>
                ";
        }
        $res .= "</select>";
        $res .= "
        </div> <!-- .form-group -->";
        return $res;
    }
}
if (!function_exists('formDropdownSelect2')) {

    function formDropdownSelect2($name = '', $options = [], $label = '', $selected_id = null, $has_null = false, $id = null, $is_disabled = false, $is_required = false)
    {
        $select2_id = rand(0, 999999999);
        $selected_id = $selected_id ?? old($name);
        $res = "
        <div class='form-group" . ($is_required ? ' required ' : '') . "'>
          <label>".__($label)."</label>
          <div class='row'>

            <select name='$name'
                class='form-control dropdown-multiple-select2  border border-secondary'
                id='$id'
                style='width: 100%;'
                data-select2-id='$select2_id'
                tabindex='-1'
                multiple='multiple'
                aria-hidden='true' "
            . ($is_disabled ? ' disabled ' : '')
            . ($is_required ? ' required ' : '')
            . "
            >";
        if ($has_null) {
            $res .= "<option value>Не выбрано</option>";
        }
        foreach ($options as $option) {
            if (!isset($option['id'])) {
                $value = $option;
                $text = $option;
            } else {
                $value = $option['id'];
                $text = $option['name'] ?? $option['title'];
            }
            $is_selected = in_array($value, $selected_id ?? []);

            $res .= "
                <option " . ($is_selected ? 'selected="selected"' : '') . " value='$value'>" . __($text) . "</option>
                ";
        }
        $res .= "</select>
            </div> <!-- .row -->
            <script>
                $('.dropdown-multiple-select2').select2();
            </script>
        </div> <!-- .form-group -->";
        return $res;
    }
}

if (!function_exists('formCheckbox')) {
    function formCheckbox($name = '', $label = '', $is_checked = false)
    {
        $is_checked = $is_checked ?: old($name);
        return "
            <div class='form-group'>
                <label class='toggle my-3'>
                  <input value='0' name='$name' type='hidden'>
                  <input value='1' class='toggle-checkbox' name='$name' " . ($is_checked ? ' checked ' : '') . "  type='checkbox'>
                  <div class='toggle-switch'></div>
                  <span class='toggle-label'>$label</span>
                </label>
            </div>
        ";
    }
}

if (!function_exists('formColorPicker')) {
    function formColorPicker($name = null, $label = null, $value = null): string
    {
        $value = $value ?? old($name);
        return "
            <div class='mb-2 col-lg-18 d-block'>
                <label>$label</label>
                <br>
                <input name='$name' value='$value' type='color' class='w-25' style='height: 2rem' rgba>
            </div>
        ";
    }
}

function formatPhoneNumber($phone)
{
    // Remove any characters other than numbers
    $phone = preg_replace('/\D+/', '', $phone);

    // Format the phone number to +7 (999) 999 9999
    if (strlen($phone) == 10) { // Assuming the number without country code is 10 digits
        return '+7 (' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 4);
    }

    // Return the raw phone if it's not the correct length
    return $phone;
}
