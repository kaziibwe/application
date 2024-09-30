<template>
  <div
    class="btn-group btn-with-tooltip-group"
    ref="filterDropdownRef"
    data-toggle="tooltip"
    :data-title="$t('filter_by')"
  >
    <button
      type="button"
      :class="activeFilterId || builderRules.length ? 'active' : ''"
      class="btn btn-default dropdown-toggle sm:tw-max-w-xs tw-truncate"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >
      <i class="fa fa-filter" aria-hidden="true"></i>
      {{ activeFilterId ? activeFilter.name : "" }}
    </button>

    <ul
      class="dropdown-menu dropdown-menu-right width300 tw-max-h-[500px] tw-overflow-y-auto"
      @click.stop=""
    >
      <div
        class="tw-py-2 tw-px-4 tw-space-x-2 tw-divide-x tw-divide-solid tw-divide-neutral-300"
      >
        <a href="#" @click="newFilter">{{ $t("filter_new") }}</a>
        <a
          href="#"
          @click="clearActiveFilter"
          v-show="activeFilterId"
          class="tw-pl-2"
        >
          {{ $t("filter_clear_active") }}
        </a>
        <a
          href="#"
          @click="initiateEditFilter"
          class="tw-pl-2"
          v-show="activeFilterId || builderRules.length > 0"
        >
          {{ $t("filter_edit") }}
        </a>
      </div>
      <li class="divider"></li>
      <div
        v-if="localSavedFilters.length === 0"
        class="tw-px-4 tw-pt-4 tw-pb-3 tw-text-balance tw-text-center tw-text-neutral-500"
      >
        {{ $t("no_filters_found") }}
      </div>
      <li
        v-for="filter in localSavedFilters"
        :key="filter.id"
        :class="filter.id == activeFilterId ? 'active' : ''"
      >
        <a
          href="#"
          @click.prevent="setFilterActive(filter), hideFiltersDropdown()"
        >
          <i
            class="fa-regular fa-star tw-text-primary-600"
            v-if="filter.is_default"
          />
          {{ filter.name }}
        </a>
      </li>
    </ul>
  </div>
  <Teleport to="body">
    <div class="modal fade filters-modal" :id="id" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg tw-max-w-[48rem]" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button
              type="button"
              class="close"
              aria-label="Close"
              @click="hideModal"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              {{ activeFilterId ? $t("filter_update") : $t("filter_create") }}
            </h4>
          </div>
          <div class="modal-body">
            <div class="tw-px-3">
              <select
                class="selectpicker"
                :id="rulesSelectId"
                data-width="100%"
                data-live-search="true"
                value=""
                name="tax"
                :data-none-selected-text="$t('filter_add_rule')"
              >
                <option
                  v-for="(rule, index) in visibleAvailableRules"
                  :key="rule.id + index"
                  :value="rule.id"
                >
                  {{ rule.label }}
                </option>
              </select>
              <div>
                <div class="tw-mt-8">
                  <span
                    v-show="builderRules.length > 1"
                    class="tw-bg-neutral-50 tw-rounded-md tw-p-2.5 tw-border tw-border-b-0 tw-border-solid tw-border-neutral-200 -tw-mb-2.5 tw-inline-block tw-z-10 relative tw-ml-5 tw-overflow-hidden"
                  >
                    <div class="radio radio-primary radio-inline">
                      <input
                        type="radio"
                        id="match_type_and"
                        :name="`match_${id}`"
                        value="and"
                        v-model="localMatchType"
                      />
                      <label for="match_type_and" class="tw-lowercase">
                        {{ $t("filter_matchtype_and") }}
                      </label>
                    </div>
                    <div class="radio radio-primary radio-inline">
                      <input
                        type="radio"
                        id="match_type_or"
                        :name="`match_${id}`"
                        value="or"
                        v-model="localMatchType"
                      />
                      <label for="match_type_or" class="tw-lowercase">
                        {{ $t("filter_matchtype_or") }}
                      </label>
                    </div>
                  </span>
                </div>
                <div
                  class="tw-py-6 tw-px-4 tw-bg-neutral-50 tw-rounded-md tw-border tw-border-solid tw-border-neutral-200 relative"
                  v-show="builderRules.length"
                >
                  <div
                    v-for="(rule, index) in builderRules"
                    :key="rule.id"
                    :class="[
                      'tw-block',
                      builderRules.length - 1 !== index ? 'tw-mb-6' : '',
                    ]"
                  >
                    <AppFiltersRule
                      :rule="rule"
                      :match-type="localMatchType"
                      :index="index"
                      @remove-requested="removeRule(index)"
                      @operator-selected="builderRules[index].operator = $event"
                      @update-rule-value="builderRules[index].value = $event"
                      @has-dynamic-value="
                        builderRules[index].has_dynamic_value = $event
                      "
                      @has-errors="rulesErrors[rule.id + index] = $event"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="filterBeingSaved || (activeFilter && canUpdate)"
                class="tw-mt-4"
              >
                <div class="form-group">
                  <label for="filter_name">
                    <span class="tw-text-danger-500">*</span>
                    {{ $t("filter_name") }}
                  </label>
                  <input
                    type="email"
                    class="form-control"
                    id="filter_name"
                    v-model="filterName"
                  />
                </div>
                <div class="checkbox checkbox-primary">
                  <input
                    type="checkbox"
                    :id="id + 'ShareFilter'"
                    :disabled="hasRulesAppliedThatAreNotVisibleToAllUsers"
                    v-model.boolean="filterIsShared"
                  />
                  <label :for="id + 'ShareFilter'">
                    {{ $t("filter_share") }}
                  </label>
                </div>
                <span
                  v-if="hasRulesAppliedThatAreNotVisibleToAllUsers"
                  class="tw-text-sm -tw-mt-2.5 tw-block tw-ml-6"
                >
                  {{ $t("filter_cannot_be_shared") }}
                </span>
                <div class="checkbox checkbox-primary">
                  <input
                    type="checkbox"
                    :id="id + 'DefaultFilter'"
                    @change="handleDefaultInputChange"
                    v-model.boolean="filterIsDefault"
                  />
                  <label :for="id + 'DefaultFilter'">
                    {{ $t("filter_mark_as_default") }}
                  </label>
                </div>
                <span v-show="showDefaultFilterInfo" class="tw-ml-6 -tw-mt-2 tw-block tw-text-neutral-500">
                  {{ $t('default_filter_info') }}
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="tw-flex tw-items-center tw-space-x-3">
              <button
                v-if="activeFilterId && canDelete"
                type="button"
                class="btn btn-danger btn-sm"
                data-toggle="tooltip"
                :data-title="$t('filter_delete')"
                @click="deleteFilter(activeFilterId)"
              >
                <i
                  class="fa-regular fa-trash-can"
                  v-show="!deleteBeingConfirmed"
                />
                {{ deleteBeingConfirmed ? $t("confirm") : "" }}
              </button>
              <div
                class="tw-flex-1 tw-flex tw-items-center tw-space-x-2 tw-self-end tw-justify-end"
              >
                <span v-if="!activeFilter" class="relative tw-mt-1">
                  <span class="tw-font-medium tw-mr-3 tw-absolute -tw-left-20">
                    {{ $t("filter_save") }}
                  </span>
                  <div class="onoffswitch">
                    <input
                      type="checkbox"
                      :id="id + 'SaveFilter'"
                      class="onoffswitch-checkbox"
                      v-model.boolean="filterBeingSaved"
                    />
                    <label class="onoffswitch-label" :for="id + 'SaveFilter'" />
                  </div>
                </span>

                <button
                  v-if="activeFilterId && !canUpdate"
                  type="button"
                  class="btn btn-sm btn-secondary"
                  @click="
                    activeFilter.is_default
                      ? unmarkAsDefault(activeFilterId)
                      : markAsDefault(activeFilterId)
                  "
                >
                  {{
                    activeFilter.is_default
                      ? $t("filter_unmark_as_default")
                      : $t("filter_mark_as_default")
                  }}
                </button>

                <button
                  type="button"
                  :class="[
                    'btn btn-primary btn-sm',
                    { 'tw-pointer-events-none': isApplyDisabled },
                  ]"
                  :disabled="isApplyDisabled"
                  @click="applyHandler"
                >
                  {{
                    filterBeingSaved || (activeFilterId && canUpdate)
                      ? $t("filter_apply_and_save")
                      : $t("filter_apply")
                  }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import AppFiltersRule from "./AppFiltersRule";
import { isValueEmpty } from "./utils";

const DEFAULT_MATCH_TYPE = "or";

const props = defineProps({
  id: { type: String, required: true },
  view: String,
  availableRules: { type: Array, required: true },
  savedFilters: { type: Array, required: true },
  rules: [Array, Object],
  matchType: { type: String, default: DEFAULT_MATCH_TYPE },
});

const rulesSelectId = `${props.id}Rules`;
const rulesSelecSelector = `#${rulesSelectId}`;

const showDefaultFilterInfo = ref(false)
const localSavedFilters = ref(props.savedFilters.map(castFilterValues));
const activeFilterId = ref(null);
const builderRules = ref([]);
const filterName = ref("");
const filterIsShared = ref(false);
const filterIsDefault = ref(false);
const filterDropdownRef = ref(null);
const filterBeingSaved = ref(false);
const localMatchType = ref(props.matchType);
const viewName = computed(() => props.view || props.id);
const rulesErrors = ref({});
const deleteBeingConfirmed = ref(false);

const tableSelector = computed(() => `table#${props.id}`);

const visibleAvailableRules = computed(() =>
  props.availableRules.filter((r) => r.visible_to_all === true)
);
const hasRulesAppliedThatAreNotVisibleToAllUsers = computed(
  () => builderRules.value.filter((r) => r.has_authorizations).length > 0
);
const canDelete = computed(
  () => app.user_is_admin || activeFilter.value.staff_id == app.user_id
);

const canUpdate = computed(
  () => app.user_is_admin || activeFilter.value.staff_id == app.user_id
);

const activeFilter = computed(
  () => localSavedFilters.value[fIdx(activeFilterId.value)] || null
);

const hasRulesWithErrors = computed(() =>
  Object.values(rulesErrors.value).some((value) => value === true)
);

const hasEmptyRulesValues = computed(
  () =>
    builderRules.value.filter(
      (r) =>
        ["is_empty", "is_not_empty"].indexOf(r.operator) === -1 &&
        isValueEmpty(r.value)
    ).length > 0
);

const isApplyDisabled = computed(
  () =>
    hasEmptyRulesValues.value ||
    hasRulesWithErrors.value ||
    (filterBeingSaved.value && !filterName.value) ||
    (builderRules.value.length === 0 && activeFilterId.value)
);

function castFilterValues(f) {
  f.id = parseInt(f.id);
  f.is_shared = String(f.is_shared) === "1";
  f.is_default = String(f.is_default) === "1";

  return f;
}

function addRule(rule) {
  builderRules.value.push({ ...rule, value: "", formatted_value: "" });
}

function removeRule(index) {
  builderRules.value.splice(index, 1);
}

function applyHandler() {
  if (
    (!filterBeingSaved.value && !activeFilterId.value) ||
    (activeFilterId.value && !canUpdate.value)
  ) {
    applyFilters();
    hideModal();
    return;
  }

  let endpoint = "filters/create";

  if (activeFilterId.value) {
    endpoint = `filters/update/${activeFilterId.value}`;
  }

  $.post(admin_url + endpoint, {
    name: filterName.value,
    is_shared: filterIsShared.value,
    is_default: filterIsDefault.value,
    identifier: props.id,
    view: viewName.value,
    rules: {
      match_type: localMatchType.value,
      rules: builderRules.value.map((r) => ({
        id: r.id,
        value: r.value,
        has_dynamic_value: r.has_dynamic_value === true,
        operator: r.operator,
        type: r.type,
      })),
    },
  }).done(function (response) {
    hideModal();
    let responseFilter = castFilterValues(JSON.parse(response));
    let filterIndex = fIdx(responseFilter.id);

    if (filterIndex !== -1) {
      localSavedFilters.value[filterIndex] = responseFilter;
    } else {
      localSavedFilters.value.push(responseFilter);
    }

    if (responseFilter.is_default) {
      localSavedFilters.value.forEach((f) => {
        f.is_default = f.id == responseFilter.id;
      });
    }
    // Sets it as active and apply
    setFilterActive(responseFilter);
    showDefaultFilterInfo.value = false
  });
}

function setFilterActive(filter, reloadTable = true) {
  activeFilterId.value = filter.id;
  builderRules.value = [...filter.builder.rules];
  localMatchType.value = filter.builder.match_type;
  filterName.value = filter.name;
  filterIsShared.value = filter.is_shared;
  filterIsDefault.value = filter.is_default;

  if (reloadTable) {
    applyFilters();
  }
}

function clearActiveFilter(e, apply = true) {
  e && e.preventDefault();
  builderRules.value = [];
  activeFilterId.value = null;
  localMatchType.value = DEFAULT_MATCH_TYPE;
  filterName.value = "";
  filterIsShared.value = false;
  filterBeingSaved.value = false;
  filterIsDefault.value = false;
  if (apply) {
    applyFilters();
  }
}

function initiateEditFilter(e) {
  e && e.preventDefault();
  if (activeFilterId.value) {
    filterName.value = activeFilter.value.name;
    filterIsShared.value = activeFilter.value.is_shared;
    filterIsDefault.value = activeFilter.value.is_default;
    builderRules.value = activeFilter.value.builder.rules;
    localMatchType.value = activeFilter.value.builder.match_type;
  } else {
    filterName.value = "";
    filterIsShared.value = false;
    filterIsDefault.value = false;
  }
  hideFiltersDropdown();
  showModal();
}

function setRulesInGlobal() {
  app.dtFilters[props.id] = {
    match_type: localMatchType.value,
    rules: builderRules.value.map((r) => ({
      id: r.id,
      value: r.value,
      has_dynamic_value: r.has_dynamic_value === true,
      operator: r.operator,
      type: r.type,
    })),
  };
}

function applyFilters() {
  setRulesInGlobal();
  $(tableSelector.value).DataTable().ajax.reload();
}

function handleRulesSelectChange(e) {
  if (e.target.value) {
    addRule(props.availableRules.find((r) => r.id === e.target.value));
    $(rulesSelecSelector).selectpicker("val", "");
  }
}

function hideFiltersDropdown() {
  filterDropdownRef.value.classList.remove("open");
}

function newFilter(e) {
  e && e.preventDefault();
  clearActiveFilter();
  hideFiltersDropdown();
  showModal();
}

function deleteFilter(filterId) {
  if (deleteBeingConfirmed.value === false) {
    deleteBeingConfirmed.value = true;
    return;
  }

  $.post(`${admin_url}filters/delete/${filterId}`).done(() => {
    clearActiveFilter();

    let filterIndex = fIdx(filterId);

    if (filterIndex !== -1) {
      localSavedFilters.value.splice(filterIndex, 1);
    }
    deleteBeingConfirmed.value = false;
    applyFilters();
  });
}

function showModal() {
  $(`.modal#${props.id}`).modal("show");
}

function hideModal() {
  $(`.modal#${props.id}`).modal("hide");
}

function fIdx(id) {
  return localSavedFilters.value.findIndex((f) => f.id === parseInt(id));
}

function markAsDefault(filterId) {
  $.post(
    `${admin_url}filters/mark_as_default/${filterId}/${props.id}/${viewName.value}`
  ).done(() => {
    localSavedFilters.value.forEach((f) => {
      f.is_default = f.id == filterId;
    });
  });
}

function unmarkAsDefault(filterId) {
  $.post(
    `${admin_url}filters/unmark_as_default/${props.id}/${viewName.value}`
  ).done(() => {
    localSavedFilters.value[fIdx(filterId)].is_default = false;
  });
}

onMounted(() => {
  $(rulesSelecSelector).on("change", handleRulesSelectChange);
});

onBeforeUnmount(() => {
  $(rulesSelecSelector).off("change", handleRulesSelectChange);
});

watch(hasRulesAppliedThatAreNotVisibleToAllUsers, (newVal) => {
  if (newVal) {
    filterIsShared.value = false;
  }
});

function changeBuilderRules(r, reload = false) {
  clearActiveFilter(null, false);
  builderRules.value = JSON.parse(JSON.stringify(r)); // deep clone
  setRulesInGlobal();
  if (reload) {
    $(tableSelector.value).DataTable().ajax.reload();
  }
}

let initialRules = props.rules
  ? Array.isArray(props.rules)
    ? props.rules
    : [props.rules]
  : null;

if (initialRules && initialRules.length) {
  changeBuilderRules(initialRules);
} else {
  const defaultFilter = localSavedFilters.value.filter(
    (f) => f.is_default === true
  )[0];

  if (defaultFilter) {
    setFilterActive(defaultFilter, false);
    setRulesInGlobal();
  }
}

watch(
  () => props.rules,
  (newVal) => {
    if (newVal) {
      changeBuilderRules(Array.isArray(newVal) ? newVal : [newVal], true);
    }
  },
  { deep: true }
);

function handleDefaultInputChange(e) {
  showDefaultFilterInfo.value = filterIsDefault.value
}

</script>
