<template>
    <div class="sidebar">
      <ul class="list-group">
        <li class="list-group-item"><a href="#">Dashboard</a></li>
        <li class="list-group-item"><a href="#">Settings</a></li>
        <li class="list-group-item"><a href="#">Profile</a></li>
      </ul>
    </div>
  </template>

  <script setup>
    // Lógica de Vue si es necesario
  </script>

  <style scoped>
    .sidebar {
      background-color: #f1f1f1;
      padding: 15px;
      height: 100%;
      width: 200px;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
    }
    .list-group-item {
      padding: 15px;
      font-size: 18px;
    }
    .list-group-item a {
      text-decoration: none;
    }
  </style>
