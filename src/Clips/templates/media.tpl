{{#if before}}
    @include before_responsive();
{{/if}}
{{#resolutions}}
    {{#if resolution}}
        {{#if before_resolution}}
            {{before_resolution}}();
        {{/if}}
        $screen-width: 0;
        $alias-width: 0;
        $max-screen-wdith: 0;
        @media screen and (min-width: {{value.value}}px) {
            $screen-width: {{value.value}} !global;
            $alias-width: {{value.alias}} !global;
            {{#if prepend_resolution}}
                {{prepend_resolution}}({{value.value}});
            {{/if}}
            {{#sasses}}
                {{#responsive_con}}
                    @include {{.}}({{../../value.value}},{{../../value.alias}});
                {{/responsive_con}}
            {{/sasses}}
            {{#if append_resolution}}
                {{append_resolution}}({{value.value}});
            {{/if}}
        }
        {{#if after_resolution}}
            {{after_resolution}}();
        {{/if}}
    {{/if}}
    {{#if section}}
        {{#if before_section}}
            {{before_section}}();
        {{/if}}
        @media screen and (min-width: {{prev_value.value}}px) and (max-width: {{value.value}}px) {
            $screen-width: {{prev_value.value}} !global;
            $alias-width: {{prev_value.alias}} !global;
            $next-screen-width: {{value.value}} !global;
			 {{#sasses}}
                {{#section_con}}
                    @include {{.}}({{value.value}},{{value.alias}},{{prev_value.value}},{{prev_value.alias}});
                {{/section_con}}
                {{#module_con}}
                    @include {{.}}({{value.value}},{{value.alias}},{{prev_value.value}},{{prev_value.alias}});
                {{/module_con}}
            {{/sasses}}
        }
        {{#if after_section}}
            {{after_section}}();
        {{/if}}
    {{/if}}
{{/resolutions}}
{{#if after}}
    @include after_responsive();
{{/if}}
