<template>
  <div class="transactions-page">
    <el-row justify="space-between" align="middle">
      <el-col>
        <h2>Transactions</h2>
      </el-col>
      <el-col>
        <el-input v-model="q" placeholder="Search trx or phone" @keyup.enter="fetch" />
      </el-col>
    </el-row>

    <transaction-table :rows="rows" @view="openDetail" @verify="onVerify" @approve="onApprove" />
    <el-pagination v-if="pagination.total" :total="pagination.total" :page-size="pagination.per_page" @current-change="onPage" />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useTransactionsStore } from '../../stores/transactions';
import TransactionTable from '../../components/TransactionTable.vue';

export default {
  components: { TransactionTable },
  setup() {
    const store = useTransactionsStore();
    const rows = ref([]);
    const pagination = ref({});
    const q = ref('');

    const fetch = async (page = 1) => {
      const res = await store.fetch({ q: q.value, page });
      rows.value = store.list;
      pagination.value = store.pagination;
    };
    onMounted(() => fetch());
    const openDetail = (id) => { this.$router.push({ name: 'transaction.detail', params: { id } }); };
    const onVerify = async (id) => { await store.verify(id); await fetch(); };
    const onApprove = async (id) => { await store.approve(id); await fetch(); };
    return { rows, pagination, q, fetch, openDetail, onVerify, onApprove };
  }
};
</script>