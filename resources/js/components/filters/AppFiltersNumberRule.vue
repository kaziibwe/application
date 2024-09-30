<template>
  <div v-if="isBetweenOperator">
    <div class="row row-no-gutters">
      <div class="col-md-6">
        <input type="number" class="form-control" v-model="localValue[0]" />
      </div>
      <div class="col-md-6">
        <input
          type="number"
          class="form-control tw-ml-1"
          v-model="localValue[1]"
        />
      </div>
    </div>
  </div>
  <input v-else type="number" class="form-control" v-model="localValue" />
</template>
<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({ rule: { type: Object, required: true } });
const emit = defineEmits(["update-rule-value"]);

const localValue = ref(props.rule.value);

const isBetweenOperator = computed(
  () =>
    props.rule.operator === "between" || props.rule.operator === "not_between"
);

watch(
  () => props.rule.operator,
  () => {
    if (isBetweenOperator.value) {
      localValue.value = [];
    } else {
      localValue.value = "";
    }
  }
);

watch(
  localValue,
  (newVal) => {
    emit("update-rule-value", newVal);
  },
  { deep: true }
);
</script>
