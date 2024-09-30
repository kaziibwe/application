<template>
  <div class="tw-relative">
    <span
      class="-tw-left-9 tw-absolute tw-bg-neutral-200 tw-border tw-border-neutral-300 tw-border-solid tw-flex tw-h-5 tw-items-center tw-rounded-md tw-text-neutral-600 tw-text-xs tw-justify-center tw-px-0.5 tw-max-w-[30px] tw-top-1/2 -tw-mt-2.5 tw-w-full tw-truncate"
      v-if="index > 0"
      v-text="$t('filter_matchtype_'+matchType)"
    />
    <div class="tw-flex tw-items-center tw-space-x-4">
      <div class="tw-flex tw-items-center tw-max-w-[170px] tw-w-full">
        <span class="tw-font-medium tw-truncate tw-mr-3" :title="rule.label">
          {{ rule.label }}
        </span>
        <div class="tw-shrink-0" v-if="rule.operators.length">
          <div class="dropdown">
            <a
              href="#"
              @click.prevent=""
              :id="dropdownId"
              class="dropdown-toggle"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              {{ $t("filter_operator_" + rule.operator) }}
              <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul
              class="dropdown-menu dropdown-menu-right"
              :aria-labelledby="dropdownId"
            >
              <li>
                <a
                  href="#"
                  @click.prevent="$emit('operator-selected', operator)"
                  v-for="operator in rule.operators"
                  :key="operator"
                >
                  {{ $t("filter_operator_" + operator) }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="tw-grow tw-max-w-[28.875rem]">
        <div v-show="!isEmptyOperator">
          <component
            :is="rulesComponentsMaps[rule.type]"
            :rule="rule"
            @update-rule-value="$emit('update-rule-value', $event)"
            @has-errors="$emit('has-errors', $event)"
            @has-dynamic-value="$emit('has-dynamic-value', $event)"
          />
        </div>
      </div>
      <a
        href="#"
        @click.prevent="$emit('remove-requested', rule)"
        class="ml-auto"
      >
        <i class="fa-regular fa-trash-can"></i>
      </a>
    </div>
  </div>
</template>
<script setup>
import { computed, watch } from "vue";
import AppFiltersTextRule from "./AppFiltersTextRule";
import AppFiltersBooleanRule from "./AppFiltersBooleanRule";
import AppFiltersNumberRule from "./AppFiltersNumberRule";
import AppFiltersSelectRule from "./AppFiltersSelectRule";
import AppFiltersMultiSelectRule from "./AppFiltersMultiSelectRule";
import AppFiltersRadioRule from "./AppFiltersRadioRule";
import AppFiltersCheckboxRule from "./AppFiltersCheckboxRule";
import AppFiltersDateRule from "./AppFiltersDateRule";

const rulesComponentsMaps = {
  TextRule: AppFiltersTextRule,
  BooleanRule: AppFiltersBooleanRule,
  NumberRule: AppFiltersNumberRule,
  SelectRule: AppFiltersSelectRule,
  MultiSelectRule: AppFiltersMultiSelectRule,
  RadioRule: AppFiltersRadioRule,
  CheckboxRule: AppFiltersCheckboxRule,
  DateRule: AppFiltersDateRule,
};

const props = defineProps({
  rule: { type: Object, required: true },
  matchType: { type: String, required: true },
  index: { type: Number, required: true },
});

const emit = defineEmits([
  "remove-requested",
  "operator-selected",
  "update-rule-value",
  "has-errors",
  "has-dynamic-value",
]);

const dropdownId = `${props.rule.id}Operators`;

const isEmptyOperator = computed(
  () => ["is_empty", "is_not_empty"].indexOf(props.rule.operator) > -1
);

watch(isEmptyOperator, (newVal) => {
  if (newVal) {
    emit("update-rule-value", null);
  }
});

if (!props.rule.operator && props.rule.operators.length > 0) {
  emit("operator-selected", props.rule.operators[0]);
}
</script>
