<template>
    <div class="table-responsive">
        <div class="text-center center-block" v-if="loading">
            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
        <table class="table" v-else>
            <thead>
                <tr>
                    <th>CUIT</th>
                    <th>Razón Social</th>
                    <th>Correo Electrónico</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="fila in filas">
                    <td>{{ fila.cuit }}</td>
                    <td>{{ fila.razon_social }}</td>
                    <td>{{ fila.email }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style>
    body{

    }
</style>
<script>
export default{
    data: function()
    {
        return {
            filas: [],
            loading: false
        }
    },
    mounted: function()
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: '/usuarios',
        method: 'GET'
        })
        .done(function($data) {
            /* alert( "success" ); */
        })
        .fail(function() {
           /*  alert( "error" ); */
        });
    }
}
</script>
