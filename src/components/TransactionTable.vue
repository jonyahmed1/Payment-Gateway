<template>
  <el-table :data="rows" stripe>
    <el-table-column prop="trx_id" label="TrxID" />
    <el-table-column prop="agent.name" label="Agent" />
    <el-table-column prop="number.phone_number" label="Number" />
    <el-table-column prop="type" label="Type" />
    <el-table-column prop="amount" label="Amount" />
    <el-table-column prop="status" label="Status" />
    <el-table-column label="Actions">
      <template #default="{ row }">
        <el-button type="primary" size="mini" @click="$emit('view', row.id)">View</el-button>
        <el-button type="success" size="mini" v-if="row.status==='pending'" @click="$emit('verify', row.id)">Verify</el-button>
        <el-button type="warning" size="mini" v-if="row.status==='verified'" @click="$emit('approve', row.id)">Approve</el-button>
      </template>
    </el-table-column>
  </el-table>
</template>
<script>
export default { name: 'TransactionTable', props: { rows: Array } };
</script>