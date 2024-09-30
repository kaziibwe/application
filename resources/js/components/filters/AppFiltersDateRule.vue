<template>
  <div ref="wrapperRef">
    <div class="tw-flex" v-if="isBetweenOperator">
      <div class="tw-flex">
        <div
          class="checkbox"
          data-toggle="tooltip"
          :data-title="$t('filter_use_dynamic_dates')"
        >
          <input
            type="checkbox"
            :id="`${dateRuleId}betweenDynamic`"
            v-model.bool="isDynamicBetweenOperator"
          />
          <label :for="`${dateRuleId}betweenDynamic`"></label>
        </div>
        <div class="date">
          <input
            v-show="!isDynamicBetweenOperator"
            type="text"
            :id="dateRuleIdStart"
            autocomplete="off"
            class="form-control datepicker"
          />
          <input
            type="text"
            v-if="isDynamicBetweenOperator"
            v-model="localValue.start"
            @input="validateDynamicBetweenDate($event, 'start')"
            placeholder="this friday"
            autocomplete="off"
            :class="[
              'form-control',
              { '!tw-border-danger-500': !dynamicBetweenDateIsValid.start },
            ]"
          />
        </div>
      </div>
      <div class="tw-ml-1">
        <input
          v-show="!isDynamicBetweenOperator"
          type="text"
          autocomplete="off"
          :id="dateRuleIdEnd"
          class="form-control datepicker"
        />
        <input
          type="text"
          v-if="isDynamicBetweenOperator"
          v-model="localValue.end"
          @input="validateDynamicBetweenDate($event, 'end')"
          placeholder="next friday"
          autocomplete="off"
          :class="[
            'form-control',
            { '!tw-border-danger-500': !dynamicBetweenDateIsValid.end },
          ]"
        />
      </div>
      <p
        class="tw-text-xs tw-top-[37px] tw-ml-7 tw-absolute"
        v-if="isDynamicBetweenOperator"
      >
        Based on PHP
        <a
          href="https://www.php.net/manual/en/function.strtotime.php"
          target="_blank"
        >
          "strtotime"
        </a>
        function.
      </p>
    </div>
    <div v-else-if="rule.operator === 'dynamic'">
      <input
        type="text"
        class="form-control"
        placeholder="this friday, first day of next month, etc..."
        :class="[
          'form-control',
          { '!tw-border-danger-500': !dynamicDateIsValid },
        ]"
        autocomplete="off"
        :id="dateRuleId"
        @input="validateDynamicDate"
      />
      <p class="tw-text-xs tw-top-[37px] tw-absolute">
        Based on PHP
        <a
          href="https://www.php.net/manual/en/function.strtotime.php"
          target="_blank"
        >
          "strtotime"
        </a>
        function. {{ dynamicDateValue !== "fails" ? dynamicDateValue : "" }}
      </p>
    </div>
    <div v-else>
      <input
        type="text"
        class="form-control datepicker"
        autocomplete="off"
        :id="dateRuleId"
      />
    </div>
  </div>
</template>
<script setup>
import {
  ref,
  watch,
  computed,
  onMounted,
  onBeforeUnmount,
} from "vue";

const props = defineProps({ rule: { type: Object, required: true } });
const emit = defineEmits([
  "update-rule-value",
  "has-errors",
  "has-dynamic-value",
]);

const localValue = ref(null);

const wrapperRef = ref(null);
const dateRuleId = `dateRule${props.rule.id}-${generatRuleId()}`;
const dateRuleIdStart = `dateRule${props.rule.id}-${generatRuleId()}-Start`;
const dateRuleIdEnd = `dateRule${props.rule.id}-${generatRuleId()}-End`;
const dateRuleSelector = `#${dateRuleId}`;
const dateRuleSelectorStart = `#${dateRuleIdStart}`;
const dateRuleSelectorEnd = `#${dateRuleIdEnd}`;
const dynamicDateIsValid = ref(true);
const dynamicBetweenDateIsValid = ref({ start: true, end: true });
const dynamicDateValue = ref("");

const isBetweenOperator = computed(
  () =>
    props.rule.operator === "between" || props.rule.operator === "not_between"
);

const isDynamicBetweenOperator = ref(
  isBetweenOperator.value && props.rule.has_dynamic_value
);

if (isDynamicBetweenOperator.value) {
  localValue.value = { start: "", end: "" };
}

function handlerDatePickerChange(e) {
  localValue.value = e.target.value;
}

function handlerDatePickerChange1(e) {
  if (!localValue.value) {
    localValue.value = { start: "", end: "" };
  }

  localValue.value.start = e.target.value;
}

function handlerDatePickerChange2(e) {
  if (!localValue.value) {
    localValue.value = { start: "", end: "" };
  }

  localValue.value.end = e.target.value;
}

function destroyDatePicker() {
  $(dateRuleSelector).off("change", handlerDatePickerChange);
  $(dateRuleSelectorStart).off("change", handlerDatePickerChange1);
  $(dateRuleSelectorEnd).off("change", handlerDatePickerChange2);
  $(dateRuleSelector).datetimepicker("destroy");
  $(dateRuleSelectorStart).datetimepicker("destroy");
  $(dateRuleSelectorEnd).datetimepicker("destroy");
}

function valueChangeHandler(value) {
  init_datepicker();

  if (isBetweenOperator.value) {
    localValue.value = { start: "", end: "" };

    const start = value ? value[0] : "";
    const end = value ? value[1] : "";

    localValue.value.start = start || "";
    localValue.value.end = end || "";

    $(dateRuleSelectorStart).val(localValue.value.start);
    $(dateRuleSelectorEnd).val(localValue.value.end);

    $(dateRuleSelectorStart).on("change", handlerDatePickerChange1);
    $(dateRuleSelectorEnd).on("change", handlerDatePickerChange2);
  } else {
    localValue.value = value || "";
    $(dateRuleSelector).val(localValue.value);

    $(dateRuleSelector).on("change", handlerDatePickerChange);
  }
}

watch(
  () => props.rule.operator,
  (newVal) => {
    isDynamicBetweenOperator.value = false;
    destroyDatePicker();
    valueChangeHandler("");
    emit("has-errors", false);
    emit("has-dynamic-value", newVal === "dynamic");
  },
  { flush: "post" }
);

watch(
  localValue,
  (newVal) => {
    emit(
      "update-rule-value",
      isBetweenOperator.value ? [newVal.start, newVal.end] : newVal
    );
  },
  { deep: true }
);

watch(
  isDynamicBetweenOperator,
  (newVal) => {
    if (newVal) {
      localValue.value.start = "";
      localValue.value.end = "";
      $(dateRuleSelectorStart).val("");
      $(dateRuleSelectorEnd).val("");
    } else {
      valueChangeHandler("");
    }
    emit("has-dynamic-value", newVal);
  },
  { flush: "post" }
);

const validateDynamicBetweenDate = debounce(function (e, type) {
  if (!e.target.value) {
    dynamicBetweenDateIsValid.value[type] = true;
    emit(
      "has-errors",
      !dynamicBetweenDateIsValid.value.start ||
        !dynamicBetweenDateIsValid.value.end
    );
    return;
  }

  $.post(`${admin_url}filters/validate_dynamic_date`, {
    value: e.target.value,
  }).done((response) => {
    dynamicBetweenDateIsValid.value[type] = response !== "fails";
    emit(
      "has-errors",
      !dynamicBetweenDateIsValid.value.start ||
        !dynamicBetweenDateIsValid.value.end
    );
  });
}, 500);

const validateDynamicDate = debounce(function (e) {
  if (!e.target.value) {
    dynamicDateIsValid.value = true;
    emit("has-errors", false);
    return;
  }

  $.post(`${admin_url}filters/validate_dynamic_date`, {
    value: e.target.value,
  }).done((response) => {
    dynamicDateIsValid.value = response !== "fails";
    dynamicDateValue.value = response;
    emit("has-errors", dynamicDateIsValid.value === false);
  });
}, 500);

onMounted(() => {
  valueChangeHandler(props.rule.formatted_value);
});

onBeforeUnmount(() => {
  emit("has-errors", false);
});

function generatRuleId(length = 5) {
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  let result = "";
  const charactersLength = characters.length;
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

function debounce(fn, delay) {
  var timeoutID = null;
  return function () {
    clearTimeout(timeoutID);
    var args = arguments;
    var that = this;
    timeoutID = setTimeout(function () {
      fn.apply(that, args);
    }, delay);
  };
}
</script>
