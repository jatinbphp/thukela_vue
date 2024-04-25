<template>
  <footer class="py-3 footer">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="mb-4 col-lg-12 mb-lg-0 text-center">
          <div
            class="text-sm text-center copyright text-muted"
            :class="this.$store.state.isRTL ? 'text-lg-end' : 'text-lg-start'"
          >
            <div class="text-sm text-white text-center copyright text-lg-start m-3" style="text-align: center !important;">© Thukela Metering | <a href=https://www.octrotus.co.za class="text-white" target="_blank">Designed &amp; Developed by Octrotus Online Solutions</a></div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script>
export default {
  name: "AppFooter",
};
</script>
<style>
.text-muted {
  color: #FFF !important;
}


</style>