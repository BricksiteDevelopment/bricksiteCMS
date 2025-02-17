<script>
    mw.require("forms.js");
    mw.require("files.js");
    mw.require("tools.js");
    mw.require("instruments.js");
</script>

<script type="text/javascript">

    var _created = false;
    var createFilePicker = function () {
        if(!_created){
            _created = true;
            var filepicker = mw.instruments.file({
                mode: 'inline'
            });
            mw.$('#file_section').append(filepicker.frame);
            filepicker.handler.on('change', function (e, url) {
                var filename = url.split('/').pop();
                setACValue(url, '_self', filename)
            })
        }
    };

    var is_searching = false;
    var hash = location.hash.replace(/#/g, "") || 'insert_link';
    var dd_autocomplete = function (id) {
        var el = $(id);
        el.on("change keyup paste focus", function (event) {
            if (!is_searching) {
                var val = el.val();
                if (event.type === 'focus') {
                    if (el.hasClass('inactive')) {
                        el.removeClass('inactive')
                    }
                    else {
                        return false;
                    }
                }
                mw.tools.ajaxSearch({keyword: val, limit: 4}, function () {
                    var lis = [];
                    var json = this;
                    var createLi = function(obj){
                        var li = document.createElement('li');
                        li.className = 'mw-dd-list-result';
                        li.onclick = function (ev) {
                            setACValue(obj.url, '_self', obj.title, obj)
                        };
                        li.innerHTML = "<a href='javascript:;'>" + obj.title + "</a>";
                        return li;
                    };
                    for (var item in json) {
                        var obj = json[item];
                        if (typeof obj === 'object') {
                            lis.push(createLi(obj));
                        }
                    }
                    var ul = el.parent().find("ul");
                    ul.find("li:gt(0)").remove();
                    ul.append(lis);
                });
            }
        });
    };

    setACValue = function () {
        mw.instrumentData.handler.trigger('change', Array.prototype.slice.call(arguments));
    };

    $(document).ready(function () {

        mw.tools.dropdown();

        dd_autocomplete('#dd_pages_search');

        mw.$("#insert_email").on('click', function () {alert(1)
            var val = mwd.getElementById('email_field').value;
            if (!val.contains('mailto:')) {
                val = 'mailto:' + val;
            }
            setACValue(val);
            return false;
        });
        mw.$("#insert_url").click(function () {
            var val = mwd.getElementById('customweburl').value;
            var target = '_self';
            if (hash === 'insert_link') {
                if (mwd.getElementById('url_target').checked == true) {
                    target = '_blank';
                }
            }
            var link_text_val = mwd.getElementById('customweburl_text').value;
            setACValue(val, target, link_text_val);

            return false;
        });
        $("#insert_from_dropdown").click(function () {
            var val = mw.$("#insert_link_list").getDropdownValue();
            setACValue(val);
            return false;
        });
        LinkTabs = mw.tabs({
            nav: ".mw-ui-btn-nav-tabs a",
            tabs: ".tab",
            onclick: function(){
                createFilePicker()
            }
        });
    });


</script>


<style type="text/css">

    #insert_link_list .mw-dropdown-content{
        position: relative;
    }

    #tabs .tab {
        display: none;
    }

    .mw-ui-row-nodrop, .media-search-holder {
        margin-bottom: 12px;
    }

    .mw-ui-box-content {
        padding-top: 20px;
    }

    #insert_link_list {
        width: 100%;
    }

    #email_field, #customweburl {
        float: left;
        width: 275px;
        margin-right: 15px;
        margin-bottom: 15px;
    }

    #available_elements {
        max-height: 400px;
        overflow: auto;
        border: 1px solid #eee;
    }

    #available_elements a {
        display: block;
        padding: 10px 12px;
        cursor: pointer;
    }

    #available_elements a:hover {
        background-color: #EEEEEE;
    }

    #available_elements a + a {
        border-top: 1px solid #eee;
    }

    #insert_link .mw-dropdown-content{
        position: relative;
    }

</style>


<div id="mw-popup-insertlink">
    <div class="">
        <div class="mw-ui-btn-nav mw-ui-btn-nav-tabs">
            <a class="mw-ui-btn active" href="javascript:;"><?php _e("Website URL"); ?></a>
            <a class="mw-ui-btn" href="javascript:;"><?php _e("Page from My Website"); ?></a>
            <a class="mw-ui-btn" href="javascript:;"><?php _e("File"); ?></a>
            <a class="mw-ui-btn" href="javascript:;"><?php _e("Email"); ?></a>
            <a class="mw-ui-btn available_elements_tab_show_hide_ctrl" href="javascript:;"><?php _e("Page Section"); ?></a>
            <a class="mw-ui-btn page-layout-btn" style="display: none;" href="javascript:;"><?php _e("Page Layout"); ?></a>
        </div>
        <div class="mw-ui-box mw-ui-box-content" id="tabs">
            <div class="tab" style="display: block">
                <div class="media-search-holder">

                    <div class="mw-ui-field-holder" id="customweburl_text_field_holder" style="display:none">
                        <label class="mw-ui-label"><?php _e("Link text"); ?></label>
                        <textarea type="text" class="mw-ui-field w100" id="customweburl_text" placeholder="Link text"></textarea>
                    </div>
                    <div class="mw-ui-field-holder">
                        <label class="mw-ui-label"><?php _e("URL"); ?></label>
                        <input type="text" class="mw-ui-field" id="customweburl" autofocus=""/>
                        <span class="mw-ui-btn mw-ui-btn-notification" id="insert_url"><?php _e("Insert"); ?></span>
                        <div class="mw-full-width m-t-20">
                            <label class="mw-ui-check mw-clear"><input type="checkbox" id="url_target"><span></span><span><?php _e("Open link in new window"); ?></span></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab">
                <div class="media-search-holder">
                    <div data-value="<?php print site_url(); ?>" id="insert_link_list" class="mw-dropdown mw-dropdown-default active">
                        <input type="text" class="mw-ui-field inactive" id="dd_pages_search" autocomplete="off" placeholder="<?php _e("Click to select"); ?>"/>
                        <span class="mw-icon-dropdown"></span>
                        <div class="mw-dropdown-content">
                            <ul class="">
                                <li class="other-action" value="-1" style="display: none;"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab">
                <div class="media-search-holder">
                    <div id="file_section"></div>
                </div>
            </div>
            <div class="tab">
                <div class="media-search-holder">
                    <input type="text" class="mw-ui-field" id="email_field" placeholder="mail@example.com"/>
                    <span class="mw-ui-btn mw-ui-btn-info right insert_the_link" id="insert_email"><?php _e("Insert"); ?></span>
                </div>
            </div>
            <div class="tab available_elements_tab_show_hide_ctrl">

                <div id="available_elements"></div>
                <script>
                    $(document).ready(function () {
                        var available_elements_tab_show_hide_ctrl_counter = 0;
                        var html = [];
                        top.$("h1[id],h12[id],h3[id],h4[id],h5[id],h6[id]", top.document.body).each(function () {
                            available_elements_tab_show_hide_ctrl_counter++;
                            html.push({id: this.id, text: this.textContent});
                            mw.$('#available_elements').append('<a data-href="#' + this.id + '"><strong>' + this.nodeName + '</strong> - ' + this.textContent + '</a>')
                        });
                        mw.$('#available_elements a').on('click', function () {
                            setACValue(top.location.href.split('#')[0] + $(this).dataset('href'));
                        });
                        if (!available_elements_tab_show_hide_ctrl_counter) {
                            mw.$('.available_elements_tab_show_hide_ctrl').hide();
                        }
                    })
                </script>
            </div>
            <div class="tab page-layout-tab">
                <label class="mw-ui-label"><?php _e('Link text'); ?></label>
                <div class="mw-field">

                    <input type="text" id="ltext">
                </div>
                <ul class="mw-ui-box mw-ui-box-content mw-ui-navigation" id="layouts-selector">

                </ul>
                <hr>
                <span class="mw-ui-btn" onclick="submitLayoutLink()"><?php _e('Insert'); ?></span>
                <script>
                    submitLayoutLink = function(){
                        var selected = $('#layouts-selector input:checked');
                        var val = top.location.href.split('#')[0] + '#mw@' + selected[0].id;
                        setACValue(val, '_self', mw.$('#ltext').val() || selected[0].id);
                    };
                    $(document).ready(function () {
                        var layoutsData = [];
                        var layouts = top.mw.$('.module[data-type="layouts"]');
                        layouts.each(function () {
                            layoutsData.push({
                                name: this.getAttribute('template').split('.')[0],
                                element: this,
                                id: this.id
                            })
                        });
                        var list = $('#layouts-selector');
                        $.each(layoutsData, function(){
                            var radio = '<input type="radio" name="layoutradio" id="' + this.id +' "><span></span>';
                            var li = $('<li><label class="mw-ui-check">' + radio + ' ' + this.name + '</label></li>');
                            var el = this.element;
                            li.on('click', function(){
                                top.mw.tools.scrollTo(el);
                            });
                            list.append(li);
                        });
                        if(layoutsData.length > 0){
                            $('.page-layout-btn').show()
                        }
                    });
                </script>

            </div>
        </div>
    </div>
</div>
