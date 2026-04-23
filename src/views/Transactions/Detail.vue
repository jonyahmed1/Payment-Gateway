<template>
  <div v-if="tx">
    <el-card>
      <h3>Transaction {{ tx.trx_id }}</h3>
      <p>Agent: {{ tx.agent.name }} | Number: {{ tx.number.phone_number }} | Amount: {{ tx.amount }} {{ tx.currency }}</p>
      <p>Status: <el-tag>{{ tx.status }}</el-tag></p>
      <div>
        <el-button type="success" v-if="tx.status==='pending'" @click="verify">Verify</el-button>
        <el-button type="warning" v-if="tx.status==='verified'" @click="approve">Approve</el-button>
        <el-button type="danger" v-if="tx.status!=='rejected'" @click="reject">Reject</el-button>
      </div>
      <div class="attachments">
        <h4>Attachments</h4>
        <div v-for="a in tx.attachments" :key="a.id">
          <img :src="a.path" style="max-width:200px" />
        </div>
      </div>
    </el-card>
  </div>
</template>
<script>
import { ref, onMounted } from 'vue';
import { useTransactionsStore } from '../../stores/transactions';
import { ElMessageBox, ElMessage } from 'element-plus';

export default {
  setup(props, { route }) {
    const store = useTransactionsStore();
    const tx = ref(null);
    const id = route.params.id;
    const load = async () => { tx.value = await store.get(id); };
    onMounted(load);
    const verify = async () => {
      await store.verify(id);
      ElMessage.success('Verified');
      load();
    };
    const approve = async () => {
      await store.approve(id);
      ElMessage.success('Approved');
      load();
    };
    const reject = async () => {
      const { value } = await ElMessageBox.prompt('Reason for rejection', 'Reject Transaction', { confirmButtonText: 'Reject', cancelButtonText: 'Cancel' });
      await store.reject(id, value);
      ElMessage.success('Rejected');
      load();
    };
    return { tx, verify, approve, reject };
  }
};
</script>