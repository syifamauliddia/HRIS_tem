<!-- LANDING -->
@if(!$req->has('id'))
<div class="bg-white rounded-lg shadow-sm border border-gray-200 min-h-[520px]">
  <div class="flex justify-between items-center px-4 py-3">
    <div class="flex items-center gap-x-3">
      <p class="mb-0 text-gray-700 font-medium">Filter Status :</p>
      <div class="flex gap-x-2">
        <button @click="filterShowData(true,1)" 
                :class="activeBtn === 1 ? 'bg-[#198754] text-white border-[#198754]' : 'border border-[#198754] text-[#198754] bg-white hover:bg-[#198754] hover:text-white'" 
                class="duration-300 transition-colors rounded px-3 py-1 text-sm">
          Active
        </button>
        <div class="flex my-auto h-5 w-px bg-gray-300"></div>
        <button @click="filterShowData(false,2)" 
                :class="activeBtn === 2 ? 'bg-[#dc3545] text-white border-[#dc3545]' : 'border border-[#dc3545] text-[#dc3545] bg-white hover:bg-[#dc3545] hover:text-white'" 
                class="duration-300 transition-colors rounded px-3 py-1 text-sm">
          Inactive
        </button>
      </div>
    </div>
    <div v-show="currentMenu?.can_create">
      <RouterLink :to="$route.path+'/create?'+(Date.parse(new Date()))" 
                  class="border border-blue-500 text-blue-500 bg-white hover:bg-blue-500 hover:text-white duration-300 transition-colors rounded px-4 py-1.5 text-sm font-medium">
        Create New
      </RouterLink>
    </div>
  </div>
  <hr class="border-gray-200 m-0">
  <div class="p-2">
    <TableApi ref='apiTable' :api="landing.api" :columns="landing.columns" :actions="landing.actions" class="max-h-[100%] overflow-y-hidden" />
  </div>
</div>
@else

<!-- CONTENT -->
@verbatim
  <div class="flex flex-col border border-gray-200 rounded-lg shadow-sm md:w-full w-full bg-white overflow-hidden">
    <!-- Header Form Grey -->
    <div class="bg-[#6c757d] text-white py-3 px-5 flex items-center">
      <Icon fa="arrow-left" class="cursor-pointer mr-4 text-xl hover:text-gray-300 transition" title="Kembali" @click="onBack"/>
      <div class="leading-tight">
        <h1 class="text-xl font-bold mb-0 text-white">Form Menu</h1>
        <p class="text-sm text-gray-200 mb-0">Master Menu</p>
      </div>
    </div>
    
    <!-- Body Form (3 Kolom Tanpa Redundant Label) -->
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">
      <!-- START COLUMN -->
      <div>
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.modul" :errorText="formErrors.modul?'failed':''"
            @input="v=>values.modul=v" :hints="formErrors.modul" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Modul"
        />
      </div>
      <div>   
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.submodul" :errorText="formErrors.submodul?'failed':''"
            @input="v=>values.submodul=v" :hints="formErrors.submodul" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Sub Modul"
        />
      </div>
      <div>
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.menu" :errorText="formErrors.menu?'failed':''"
            @input="v=>values.menu=v" :hints="formErrors.menu" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Menu"
        />
      </div>
      <div>
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.path" :errorText="formErrors.path?'failed':''"
            @input="v=>values.path=v" :hints="formErrors.path" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Path, Ex: /m_menu"
        />
      </div>
      <div>
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.endpoint" :errorText="formErrors.endpoint?'failed':''"
            @input="v=>values.endpoint=v" :hints="formErrors.endpoint" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Endpoint, Ex: /m_menu"
        />
      </div>
      <div>
        <FieldX :bind="{ readonly: !actionText }" class="w-full"
            :value="values.icon" :errorText="formErrors.icon?'failed':''"
            @input="v=>values.icon=v" :hints="formErrors.icon" 
            :check="false"
            label=""
            placeholder="Tuliskan Nama Icon, Ex: bookmark"
        />
      </div>
      <div>
        <FieldNumber :bind="{ readonly: !actionText }" class="w-full"
            :value="values.sequence" :errorText="formErrors.sequence?'failed':''"
            @input="v=>values.sequence=v" :hints="formErrors.sequence" 
            :check="false"
            label=""
            placeholder="Tuliskan Sequence, Ex: 2.4"
        />
      </div>
      <div>
        <FieldX class="w-full"
            :bind="{ readonly: !actionText }"
            :value="values.description" :errorText="formErrors.description?'failed':''"
            @input="v=>values.description=v"
            type="textarea"
            :hints="formErrors.description"
            label=""
            placeholder="Tuliskan Keterangan"
            :check="false" />
      </div>
      <div>
        <FieldSelect
          :bind="{ disabled: !actionText, clearable:false }" class="w-full"
          :value="values.is_active" @input="v=>values.is_active=v"
          :errorText="formErrors.is_active?'failed':''" 
          :hints="formErrors.is_active"
          valueField="id" displayField="key"
          :options="[{'id' : 1 , 'key' : 'Active'},{'id': 0, 'key' : 'InActive'}]"
          placeholder="Pilih Status" label="Status" :check="false"
        />
      </div>
      <!-- END COLUMN -->
    </div>
    
    <hr class="border-gray-100 m-0">
    
    <!-- ACTION BUTTON START -->
    <div class="flex flex-row items-center justify-end space-x-3 p-4 bg-gray-50 rounded-b-lg">
      <span class="text-gray-500 text-sm italic me-2">Tekan CTRL + S untuk shortcut Save Data</span>
      <button 
        class="bg-[#dc3545] text-white font-medium hover:bg-[#c82333] transition-colors duration-200 rounded px-4 py-2 flex items-center gap-2"
        v-show="actionText" 
        @click="onReset(true)" 
      >
        <icon fa="times" />
        Reset
      </button>
      <button 
        class="bg-[#198754] text-white font-medium hover:bg-[#157347] transition-colors duration-200 rounded px-4 py-2 flex items-center gap-2"
        v-show="actionText && (currentMenu?.can_create || currentMenu?.can_update)" 
        @click="onSave" 
      >
        <icon fa="save" />
        Simpan
      </button>
    </div>
  </div>
@endverbatim
@endif

<script setup>
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { ref, readonly, reactive, inject, onMounted, onBeforeMount, watchEffect, onActivated } from 'vue'

const router = useRouter()
const route = useRoute()
const store = inject('store')
const swal = inject('swal')

const isRead = route.params.id && route.params.id !== 'create'
const actionText = ref(route.params.id === 'create' ? 'Tambah' : route.query.action)
const isBadForm = ref(false)
const isRequesting = ref(false)
const modulPath = route.params.modul || 'm_menu'
const currentMenu = store.currentMenu
const apiTable = ref(null)
const formErrors = ref({})
const tsId = `ts=`+(Date.parse(new Date()))

// ------------------------------ PERSIAPAN
const endpointApi = '/m_menu'
onBeforeMount(()=>{
  document.title = 'Master Menu'
})

//  @if( $id )------------------- VALUES FORM ! PENTING JANGAN DIHAPUS
let initialValues = {}
const changedValues = []

const values = reactive({
   is_active: 1
})

onBeforeMount(async () => {
  if (isRead) {
    //  READ DATA
    try {
      const editedId = route.params.id
      const dataURL = `${store.server.url_backend}/operation${endpointApi}/${editedId}`
      isRequesting.value = true

      const params = { join: false, transform: false }
      const fixedParams = new URLSearchParams(params)
      const res = await fetch(dataURL + '?' + fixedParams, {
        headers: {
          'Content-Type': 'Application/json',
          Authorization: `${store.user.token_type} ${store.user.token}`
        },
      })
      if (!res.ok) throw new Error("Failed when trying to read data")
      const resultJson = await res.json()
      initialValues = resultJson.data
      initialValues.is_active=initialValues.is_active?1:0
    } catch (err) {
      isBadForm.value = true
      swal.fire({
        icon: 'error',
        text: err,
        allowOutsideClick: false,
        confirmButtonText: 'Kembali',
      }).then(() => {
        router.back()
      })
    }
    isRequesting.value = false
  }

  for (const key in initialValues) {
    values[key] = initialValues[key]
  }
})

function onBack() {
  router.replace('/' + modulPath)
  return
}

function onReset() {
  swal.fire({
    icon: 'warning',
    text: 'Anda yakin akan mereset data ini?',
    showDenyButton: true
  }).then((res) => {
    if (res.isConfirmed) {
      const newValues = {
        modul: '',
        submodul: '',
        menu: '',
        path: '',
        endpoint: '',
        icon: '',
        sequence: '',
        description: '',
        note: '',
      };
      
      for (const key in newValues) {
        if (newValues.hasOwnProperty(key)) {
          values[key] = newValues[key];
        }
      }
    }
  })
}

async function onSave() {
    try {
      const isCreating = ['Create','Copy','Tambah'].includes(actionText.value);
      const dataURL = `${store.server.url_backend}/operation${endpointApi}${isCreating ? '' : ('/' + route.params.id)}`;
      isRequesting.value = true;
      values.is_active = values.is_active ? 1 : 0
      const res = await fetch(dataURL, {
        method: isCreating ? 'POST' : 'PUT',
        headers: {
          'Content-Type': 'Application/json',
          Authorization: `${store.user.token_type} ${store.user.token}`
        },
        body: JSON.stringify(values)
      });
      if (!res.ok) {
        if ([400, 422].includes(res.status)) {
          const responseJson = await res.json();
          formErrors.value = responseJson.errors || {};
          throw (responseJson.errors.length ? responseJson.errors[0] : responseJson.message || "Failed when trying to post data");
        } else {
          throw ("Failed when trying to post data");
        }
      }
      router.replace('/' + modulPath + '?reload='+(Date.parse(new Date())));
    } catch (err) {
      isBadForm.value = true;
      swal.fire({
        icon: 'error',
        text: err
      });
    }
    isRequesting.value = false;
}

//  @else----------------------- LANDING

const activeBtn = ref()

function filterShowData(params,noBtn){
  if(activeBtn.value === noBtn){
    activeBtn.value = null
  }else{
    activeBtn.value = noBtn
  }
  
  if(activeBtn.value == null){
    landing.api.params.where = null
  }else if(params){
    landing.api.params.where = `this.is_active=true`
  }else{
    landing.api.params.where = `this.is_active=false`
  }

  apiTable.value.reload()
}

const landing = reactive({
  actions: [
    {
      icon: 'trash',
      class: 'bg-red-600 text-light-100',
      title: "Hapus",
      show: (row) => (currentMenu?.can_delete),
      click(row) {
        swal.fire({
          icon: 'warning',
          text: 'Hapus Data Terpilih?',
          confirmButtonText: 'Yes',
          showDenyButton: true,
        }).then(async (result) => {
          if (result.isConfirmed) {
            try {
              const dataURL = `${store.server.url_backend}/operation${endpointApi}/${row.id}`
              isRequesting.value = true
              const res = await fetch(dataURL, {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'Application/json',
                  Authorization: `${store.user.token_type} ${store.user.token}`
                }
              })
              if (!res.ok) {
                const resultJson = await res.json()
                throw (resultJson.message || "Failed when trying to remove data")
              }
              apiTable.value.reload()
            } catch (err) {
              isBadForm.value = true
              swal.fire({
                icon: 'error',
                text: err
              })
            }
            isRequesting.value = false
          }
        })
      }
    },
    {
      icon: 'eye',
      title: "Read",
      class: 'bg-green-600 text-light-100',
      show: (row) => (currentMenu?.can_read),
      click(row) {
        router.push(`${route.path}/${row.id}?`+tsId)
      }
    },
    {
      icon: 'edit',
      title: "Edit",
      class: 'bg-blue-600 text-light-100',
      show: (row) => currentMenu?.can_update === true,
      click(row) {
        router.push(`${route.path}/${row.id}?action=Edit&`+tsId)
      }
    },
    {
      icon: 'copy',
      title: "Copy",
      class: 'bg-gray-600 text-light-100',
      show: (row) => currentMenu?.can_create === true,
      click(row) {
        router.push(`${route.path}/${row.id}?action=Copy&`+tsId)
      }
    }
  ],
  api: {
    url: currentMenu?.can_read ? `${store.server.url_backend}/operation${endpointApi}` : '',
    headers: {
      'Content-Type': 'Application/json',
      authorization: `${store.user.token_type} ${store.user.token}`
    },
    params: {
      simplest: true,
      searchfield: 'this.id, this.modul, this.submodul, this.menu, this.path, this.icon, this.sequence, this.is_active'
    },
    onsuccess(response) {
      response.page = response.current_page
      response.hasNext = response.has_next
      return response
    }
  },
  columns: [
    {
      headerName: 'No',
      valueGetter: (params) => params.node.rowIndex + 1,
      width: 60,
      sortable: true,
      resizable: true,
      filter: true,
      cellClass: ['justify-center', 'bg-gray-50', 'border-r', '!border-gray-200']
    },
    {
      field: 'modul',
      filter: true,
      sortable: true,
      flex: 1,
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      cellClass: ['border-r', '!border-gray-200', 'justify-start']
    },
    {
      field: 'submodul',
      filter: true,
      sortable: true,
      flex: 1,
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      cellClass: ['border-r', '!border-gray-200', 'justify-start']
    },
    {
      field: 'menu',
      filter: true,
      sortable: true,
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      flex: 1,
      cellClass: ['border-r', '!border-gray-200', 'justify-start']
    },
    {
      field: 'path',
      filter: true,
      sortable: true,
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      flex: 1,
      cellClass: ['border-r', '!border-gray-200', 'justify-start']
    },
    {
      field: 'icon',
      filter: true,
      sortable: true,
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      flex: 1,
      cellClass: ['border-r', '!border-gray-200', 'justify-start']
    },
    {
      field: 'sequence',
      filter: true,
      sortable: true,
      sort: 'asc', // Mengurutkan urutan sequence dari terkecil ke terbesar
      filter: 'ColFilter',
      resizable: true,
      wrapText: true,
      flex: 1,
      cellClass: ['border-r', '!border-gray-200', 'justify-end']
    },
    {
      field: 'is_active',
      filter: true,
      sortable: true,
      flex: 1,
      cellClass: ['border-r', '!border-gray-200', 'justify-center'],
      cellRenderer: ({ value }) => {
        return value === true
          ? `<span class="text-[#198754] font-semibold text-sm">Active</span>`
          : `<span class="text-[#dc3545] font-semibold text-sm">Inactive</span>`
      }
    },
  ]
})

onActivated(() => {
  if (apiTable.value) {
    if (route.query.reload) {
      apiTable.value.reload()
    }
  }
})

watchEffect(() => store.commit('set', ['isRequesting', isRequesting.value]))
</script>