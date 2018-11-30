<template>
  <div id="app">
    {{ message }}
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

@Component
export default class App extends Vue {
  public message: string = "STATE-1";

  public created(): void {
    setTimeout(() => {
      this.message = "STATE-2";
    }, 5000);
  }
}
</script>

<style scoped></style>
