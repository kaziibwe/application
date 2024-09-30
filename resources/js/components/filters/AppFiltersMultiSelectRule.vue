<template>
  <select
    class="selectpicker"
    data-live-search="true"
    multiple
    :id="selectRuleId"
    data-width="100%"
    data-none-selected-text=""
  >
    <option
      v-for="option in rule.options"
      :key="option.value"
      :value="option.value"
      :selected="rule.value.indexOf(option.value) > -1"
      :data-subtext="option.subtext"
    >
      {{ option.label }}
    </option>
  </select>
</template>
<script setup>
import { onBeforeUnmount, onMounted, watch } from "vue";

const props = defineProps({ rule: { type: Object, required: true } });
const emit = defineEmits(["update-rule-value"]);

const selectRuleId = `multiSelectRule${props.rule.id}`;
const selectRuleSelector = `#${selectRuleId}`;

watch(
  () => props.rule.value,
  (newVal) => {
    $(selectRuleSelector).selectpicker("val", newVal);
  }
);

function updateRuleValue(e) {
  emit("update-rule-value", $(selectRuleSelector).selectpicker("val"));
}

onMounted(() => {
  init_selectpicker();
  $(selectRuleSelector).on("change", updateRuleValue);
});

onBeforeUnmount(() => {
  $(selectRuleSelector).off("change", updateRuleValue);
});
</script>
