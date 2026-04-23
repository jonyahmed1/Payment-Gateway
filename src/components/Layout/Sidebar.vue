<template>
  <el-menu :default-active="$route.name" class="sidebar" router>
    <el-menu-item index="dashboard" @click="$router.push('/')">Dashboard</el-menu-item>
    <el-menu-item index="transactions" @click="$router.push('/transactions')">Transactions</el-menu-item>
    <el-sub-menu index="agents">
      <template #title>Agents</template>
      <el-menu-item @click="$router.push('/agents/bkash')">bKash</el-menu-item>
      <el-menu-item @click="$router.push('/agents/nagad')">Nagad</el-menu-item>
      <el-menu-item @click="$router.push('/agents/rocket')">Rocket</el-menu-item>
    </el-sub-menu>
    <el-menu-item index="reports">Reports</el-menu-item>
    <el-menu-item index="settings">Settings</el-menu-item>
  </el-menu>
</template>
<script>
export default { name: 'Sidebar' };
</script>
<style scoped>
.sidebar { width: 220px; height: 100vh; }
</style>