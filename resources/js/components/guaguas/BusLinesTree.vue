<template>
  <transition name="slide">
    <div 
      v-if="visible"
      class="absolute top-0 right-0 w-full sm:w-72 md:w-80 bg-gray-800 border-l border-gray-700 z-[999] overflow-y-auto rounded-r-lg shadow-xl"
      :class="heightClass"
    >
      <!-- Header -->
      <div class="p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gray-800 z-10">
        <h3 class="font-bold text-base sm:text-lg mb-1">{{ $t('guaguas.filterTitle') }}</h3>
        <p class="text-xs text-gray-400">{{ $t('guaguas.filterDescription') }}</p>
      </div>
      
      <!-- Tree content -->
      <div class="p-4">
        <el-tree
          ref="treeRef"
          :data="treeData"
          :props="{ children: 'children', label: 'label' }"
          node-key="id"
          :default-expanded-keys="expandedKeys"
          @node-expand="handleNodeExpand"
          @node-collapse="handleNodeCollapse"
          @node-click="handleNodeClick"
          class="bg-transparent text-white"
        >
          <template #default="{ node, data }">
            <span class="flex items-center gap-2 w-full">
              <!-- Bus indicator dot -->
              <span 
                v-if="data.type === 'bus'"
                class="size-3 rounded-full flex-shrink-0"
                :class="getBusIndicatorClass(data)"
              ></span>
              
              <!-- Line indicator dot -->
              <span 
                v-else-if="data.type === 'line'"
                class="size-3 rounded-full flex-shrink-0"
                :class="getLineIndicatorClass(data)"
              ></span>
              
              <!-- Label -->
              <span 
                class="text-sm truncate" 
                :class="getLabelClass(data)"
              >
                {{ node.label }}
                <span 
                  v-if="data.type === 'line' && isLineSelected(data.line, data.company)" 
                  class="ml-1 text-green-400"
                >✓</span>
              </span>
            </span>
          </template>
        </el-tree>
      </div>
    </div>
  </transition>
</template>

<script setup>
/**
 * @component BusLinesTree
 * @description Collapsible tree panel for bus line selection and filtering
 * 
 * @example
 * <BusLinesTree
 *   :visible="treeVisible"
 *   :tree-data="busTreeData"
 *   :hidden-bus-ids="hiddenBusIds"
 *   :is-line-selected="isLineSelected"
 *   @node-click="handleNodeClick"
 *   @node-expand="handleNodeExpand"
 *   @node-collapse="handleNodeCollapse"
 * />
 */
import { ref } from 'vue';
import { ElTree } from 'element-plus';

const props = defineProps({
  /**
   * Whether the tree panel is visible
   */
  visible: {
    type: Boolean,
    default: true
  },
  /**
   * Tree data structure for el-tree
   */
  treeData: {
    type: Array,
    required: true
  },
  /**
   * Set of hidden bus IDs
   */
  hiddenBusIds: {
    type: [Set, Array, Object],
    default: () => new Set()
  },
  /**
   * Default expanded node keys
   */
  expandedKeys: {
    type: Array,
    default: () => []
  },
  /**
   * Function to check if a line is selected
   */
  isLineSelected: {
    type: Function,
    required: true
  },
  /**
   * Height class for the container
   */
  heightClass: {
    type: String,
    default: 'h-[400px] sm:h-[500px] md:h-[600px] lg:h-[700px] xl:h-[800px]'
  }
});

const emit = defineEmits(['node-click', 'node-expand', 'node-collapse']);

// Template ref
const treeRef = ref(null);

/**
 * Get indicator class for bus items
 */
const getBusIndicatorClass = (data) => {
  if (props.hiddenBusIds.has(data.id)) {
    return 'bg-gray-500';
  }
  if (data.bus?.company === 'municipales') {
    return 'bg-yellow-400';
  }
  if (data.bus?.company === 'global') {
    return 'bg-blue-500';
  }
  return 'bg-purple-500';
};

/**
 * Get indicator class for line items
 */
const getLineIndicatorClass = (data) => {
  const baseClass = data.company === 'municipales' 
    ? 'bg-yellow-400' 
    : data.company === 'global' 
      ? 'bg-blue-500' 
      : 'bg-purple-500';
  
  const ringClass = props.isLineSelected(data.line, data.company) 
    ? 'ring-2 ring-white' 
    : '';
  
  return `${baseClass} ${ringClass}`;
};

/**
 * Get label class based on item type and state
 */
const getLabelClass = (data) => {
  if (data.type === 'bus') {
    return props.hiddenBusIds.has(data.id) ? 'opacity-50' : '';
  }
  
  if (data.type === 'line') {
    return props.isLineSelected(data.line, data.company)
      ? 'font-bold text-white'
      : 'opacity-60';
  }
  
  return '';
};

// Event handlers
const handleNodeClick = (data, node) => {
  emit('node-click', data, node);
};

const handleNodeExpand = (data, node) => {
  emit('node-expand', data, node);
};

const handleNodeCollapse = (data, node) => {
  emit('node-collapse', data, node);
};

// Expose tree ref for external access if needed
defineExpose({
  treeRef
});
</script>

<style scoped>
/* Slide transition */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* Override Element Plus tree styles for dark theme */
:deep(.el-tree) {
  background: transparent;
  color: white;
}

:deep(.el-tree-node__content) {
  background: transparent !important;
}

:deep(.el-tree-node__content:hover) {
  background: rgba(255, 255, 255, 0.1) !important;
}

:deep(.el-tree-node__expand-icon) {
  color: #9ca3af;
}

:deep(.el-tree-node__expand-icon.is-leaf) {
  color: transparent;
}
</style>
