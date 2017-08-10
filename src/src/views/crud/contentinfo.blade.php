<style>
    h3.table-fields {
        display: inline;
    }
    code.table-fields {
        margin-left: 15px;
    }
</style>

<h3 class="table-fields">{{ $content->name }}</h3>
<code class="table-fields">table: {{ $content->table }}; model: {{ $content->model }}</code>