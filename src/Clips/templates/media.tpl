{{#before}}
    @include before_responsive();
{{/before}}
{{#resolutions}}
    {{#resolution}}
        {{#before_resolution}}
            {{before_resolution}}();
        {{/before_resolution}}
        $screen-width: 0;
        $alias-width: 0;
        $max-screen-wdith: 0;
        @media screen and (min-width: {{value.value}}px) {
            $screen-width: {{value.value}};
            $alias-width: {{value.alias}};
            {{#prepend_resolution}}
                {{prepend_resolution}}({{value.value}});
            {{/prepend_resolution}}
            {{#sasses}}
                {{#responsive_con}}
                    @include {{.}}({{value.value}},{{value.alias}});
                {{/responsive_con}}
            {{/sasses}}
            {{#append_resolution}}
                {{append_resolution}}({{value.value}});
            {{/append_resolution}}
        }
        {{#after_resolution}}
            {{after_resolution}}();
        {{/after_resolution}}
    {{/resolution}}
    {{#section}}
        {{#before_section}}
            {{before_section}}();
        {{/before_section}}
        @media screen and (min-width: {{prev_value.value}}px) and (max-width: {{value.value}}px) {
            $screen-width: {{prev_value.value}};
            $alias-width: {{prev_value.alias}};
            $next-screen-width: {{value.value}};
            {{#sasses}}
                {{#section_con}}
                    @include {{.}}({{value.value}},{{value.alias}},{{prev_value.value}},{{prev_value.alias}});
                {{/section_con}}
                {{#module_con}}
                    @include {{.}}({{value.value}},{{value.alias}},{{prev_value.value}},{{prev_value.alias}});
                {{/module_con}}
            {{/sasses}}
        }
        {{#after_section}}
            {{after_section}}();
        {{/after_section}}
    {{/section}}
{{/resolutions}}
{{#after}}
    @include after_responsive();
{{/after}}