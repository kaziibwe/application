<template>
  <input
    type="text"
    class="form-control"
    autocomplete="off"
    :value="rule.value"
    @input="$emit('update-rule-value', $event.target.value)"
  />
</template>
<script setup>
defineProps({ rule: { type: Object, required: true } });
defineEmits(['update-rule-value'])
</script>
