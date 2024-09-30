<template>
  <div
    class="radio radio-primary radio-inline"
    v-for="option in rule.options"
    :key="option.value"
  >
    <input
      type="radio"
      :id="`opt_1_${rule.id}_${option.value}`"
      :name="`rule_${rule.id}`"
      :value="option.value"
      v-model="localValue"
    />
    <label :for="`opt_1_${rule.id}_${option.value}`"> {{ option.label }}</label>
  </div>
</template>
<script setup>
import { ref, watch } from "vue";

const props = defineProps({ rule: { type: Object, required: true } });
const emit = defineEmits(["update-rule-value"]);

const localValue = ref(props.rule.value);

watch(localValue, (newVal) => {
  emit("update-rule-value", newVal);
});
</script>
