
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { useApi } from '@/composables/useApi';
  import ConfirmDialog from '@/components/ConfirmDialog.vue';


// employee type
  type Employee = {
    employeeId: number;
    company: string;
    firstName: string;
    lastName: string;
    jobDescription: string;
    grade: string | null;
    decision: string | null;
  };
  // metadata type
  type Meta ={
    gradeOptions: string[];
    ceoDecision: string[];
  }
// state variables
  const grid1 = ref<Employee[]>([]);
  const grid2 = ref<Employee[]>([]);
  const meta = ref<Meta>({gradeOptions: [], ceoDecision: []});

  const loading = ref(false);
  const savingIds = ref<Set<number>>(new Set());
  const errorMsg = ref<string | null>(null);

  const showConfirm = ref(false);
  let selectedEmp: Employee | null = null;
  
  // API methods
  const { get, patch, del } = useApi();

// fetch unhandled employees
  async function fetchUnHandled(){
    try{
      loading.value = true;
      errorMsg.value = null;

      const result = await get('/employees/pending');
      grid1.value = (result?.data??[]) as Employee[];
      
      if(result?.meta){
        meta.value = {
          ...meta.value,
          gradeOptions: result.meta.gradeOptions ?? meta.value.gradeOptions,
          ceoDecision: result.meta.ceoDecision ?? meta.value.ceoDecision,
        }
      }
    }
    catch (e){
      errorMsg.value = 'Failed to fetch unhandled employees.';
      console.error(e);
    }
    finally{
      loading.value = false;
    }
  }

  // fetch all employees
  async function fetchAll(){
    try{
      loading.value = true;
      errorMsg.value = null;

      const result = await get('/employees/all');
      grid2.value = (result?.data??[]) as Employee[];
      
      if(result?.meta){
        meta.value = {
          ...meta.value,
          gradeOptions: result.meta.gradeOptions ?? meta.value.gradeOptions,
          ceoDecision: result.meta.ceoDecision ?? meta.value.ceoDecision,
        }
      }
    }
    catch(e){
      errorMsg.value = 'Failed to fetch handled employees.';
      console.error(e);
    }
    finally{
      loading.value = false;
    }
  }
// to refresh both grids after an update
  async function refresh(){
    await Promise.all([fetchUnHandled(), fetchAll()]);
  }

  // handle grade change
  async function onChangeGrade(emp: Employee, newGrade: string){
    // if(savingIds.value.has(emp.employeeId)){
    //   return;
    // }

    try{
      savingIds.value.add(emp.employeeId);
      await patch(`/employees/${emp.employeeId}/grade`, {grade: newGrade});
      await refresh();
    }
    catch(e){
      errorMsg.value = `Failed to update grade for ${emp.firstName} ${emp.lastName}.`;
      console.error(e);
    }
    finally{
      savingIds.value.delete(emp.employeeId);
    }
  }


  // handle decision change
  async function onChangeDecision(emp: Employee, newDecision: string){
    // if(savingIds.value.has(emp.employeeId)){
    //   return;
    // }

    try{
      savingIds.value.add(emp.employeeId);
      await patch(`/employees/${emp.employeeId}/decision`, {decision: newDecision});
      await refresh();
    }
    catch(e){
      errorMsg.value = `Failed to update decision for ${emp.firstName} ${emp.lastName}.`;
      console.error(e);
    }
    finally{
      savingIds.value.delete(emp.employeeId);
    }
  }

  function openDeleteModal(emp: Employee){
    selectedEmp = emp;
    showConfirm.value = true;
  }

  async function confirmDelete(){
    if(!selectedEmp){
      return;
    }

    try{
      savingIds.value.add(selectedEmp.employeeId);
      await del(`/employees/${selectedEmp.employeeId}/decision`);
      await refresh();
    }
    catch(e){
      errorMsg.value = `Failed to delete decision for ${selectedEmp.firstName} ${selectedEmp.lastName}.`;
      console.error(e);
    }
    finally{
      savingIds.value.delete(selectedEmp.employeeId);
      showConfirm.value = false;
      selectedEmp = null;
    }
  }

  onMounted(async () =>{
    await fetchUnHandled();
    await fetchAll();
  });

  </script>
  
  
  <template>

<section class="p-6 space-y-3">
    <h2 class="text-xl font-medium">Grid #1 — Not Yet Handled</h2>

    <div v-if="errorMsg" class="rounded bg-red-50 text-red-700 p-2 text-sm">
      {{ errorMsg }}
    </div>

    <div class="overflow-x-auto rounded border">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-left text-xs text-gray-600 uppercase">
          <tr>
            <th class="px-3 py-2">Company</th>
            <th class="px-3 py-2">First</th>
            <th class="px-3 py-2">Last</th>
            <th class="px-3 py-2">Position</th>
            <th class="px-3 py-2">Grade</th>
            <th class="px-3 py-2">CEO Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in grid1" :key="emp.employeeId" class="border-t">
            <td class="px-3 py-2">{{ emp.company }}</td>
            <td class="px-3 py-2">{{ emp.firstName }}</td>
            <td class="px-3 py-2">{{ emp.lastName }}</td>
            <td class="px-3 py-2">{{ emp.jobDescription }}</td>
            <td class="px-3 py-2">
              <select 
                class="border rounded px-2 py-1 text-sm disabled:opacity-50"
                :disabled="savingIds.has(emp.employeeId) || loading"
                :value="emp.grade ?? ''"
                @change="(e: any) => onChangeGrade(emp, e.target.value)"  
              >
                <option value="" disabled>Select grade</option>
                <option v-for="g in meta.gradeOptions" :key="g" :value="g">{{ g }}</option>
              </select>
            </td>
            <td class="px-3 py-2">
              <select class="border rounded px-2 py-1 text-sm disabled:opacity-50"
                :disabled="savingIds.has(emp.employeeId) || loading " 
                :value="emp.decision"
                @change="(e: any) => onChangeDecision(emp, e.target.value)"
                >
                <option v-for="d in meta.ceoDecision" :key="d" :value="d">{{ d }}</option>
              </select>
            </td>
          </tr>
          <tr v-if="!loading && grid1.length === 0">
            <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
              All employees are handled
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading…</div>
  </section>


  <section class="p-6 space-y-3">
    <h2 class="text-xl font-medium">Grid #2 - All employees</h2>

    <div class="overflow-x-auto rounded border">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-left text-xs text-gray-600 uppercase">
          <tr>
            <th class="px-3 py-2">Company</th>
            <th class="px-3 py-2">First</th>
            <th class="px-3 py-2">Last</th>
            <th class="px-3 py-2">Position</th>
            <th class="px-3 py-2">Grade</th>
            <th class="px-3 py-2">Decision</th>
            <th class="px-3 py-2">CEO Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in grid2" :key="emp.employeeId" class="border-t">
            <td class="px-3 py-2">{{ emp.company }}</td>
            <td class="px-3 py-2">{{ emp.firstName }}</td>
            <td class="px-3 py-2">{{ emp.lastName }}</td>
            <td class="px-3 py-2">{{ emp.jobDescription }}</td>
            <td class="px-3 py-2">{{ emp.grade }}</td>
            <td class="px-3 py-2">{{ emp.decision }}</td>
            <td class="px-3 py-2">
             <button 
             class="text-sm px-3 py-2 rounded border hover:bg-gray-50 disabled:opacity-50"
             :disabled="savingIds.has(emp.employeeId) || loading"
             @click="openDeleteModal(emp)"
             >X</button>
            </td>
          </tr>
          <tr v-if="!loading && grid2.length === 0">
            <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
              No employees found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </section>

  <ConfirmDialog 
    v-model="showConfirm" 
    title="Confirm Delete" 
    message="Are you sure you want to delete the CEO decision for this employee?"
    @confirm="confirmDelete"
  />

  
  </template>
  