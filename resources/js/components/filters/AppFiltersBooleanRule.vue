<template>
  <div class="radio radio-primary radio-inline">
    <input
      type="radio"
      :id="`y_opt_1_${rule.id}`"
      :name="`rule_${rule.id}`"
      value="1"
      v-model="localValue"
    />
    <label :for="`y_opt_1_${rule.id}`">
      {{ $t("filter_boolean_yes") }}
    </label>
  </div>
  <div class="radio radio-primary radio-inline">
    <input
      type="radio"
      :id="`y_opt_0_${rule.id}`"
      :name="`rule_${rule.id}`"
      value="0"
      v-model="localValue"
    />
    <label :for="`y_opt_0_${rule.id}`">
      {{ $t("filter_boolean_no") }}
    </label>
  </div>
</template>
<script setup>
import { ref, watch } from "vue";

defineOptions({ inheritAttrs: false });
const props = defineProps({ rule: { type: Object, required: true } });
const emit = defineEmits(["update-rule-value"]);

const localValue = ref(props.rule.value);

watch(localValue, (newVal) => {
  emit("update-rule-value", newVal);
});
</script>
