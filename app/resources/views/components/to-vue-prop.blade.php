<script setup></script>
<div>
<p>Mixed: <span v-text="mixed" /> (<span v-text="typeof mixed" />)</p>
<p>String: <span v-text="string" /> (<span v-text="typeof string" />)</p>
<p>Default String: <span v-text="defaultString" /> (<span v-text="typeof defaultString" />)</p>
<p>Nullable String: <span v-text="nullableString" /> (<span v-text="typeof nullableString" />)</p>
<p>Int: <span v-text="int" /> (<span v-text="typeof int" />)</p>
<p>Bool: <span v-text="bool" /> (<span v-text="typeof bool" />)</p>
<p>Array: <span v-text="array" /> (<span v-text="typeof array" />)</p>
<p>Object: <span v-text="object" /> (<span v-text="typeof object" />)</p>
<p>Nullable Int: <span v-text="nullableInt" /> (<span v-text="typeof nullableInt" />)</p>
<p>Nullable Bool: <span v-text="nullableBool" /> (<span v-text="typeof nullableBool" />)</p>
<p>Nullable Array: <span v-text="nullableArray" /> (<span v-text="typeof nullableArray" />)</p>
<p>Nullable Object: <span v-text="nullableObject" /> (<span v-text="typeof nullableObject" />)</p>
<p>Default Int: <span v-text="defaultInt" /> (<span v-text="typeof defaultInt" />)</p>
<p>Default Bool: <span v-text="defaultBool" /> (<span v-text="typeof defaultBool" />)</p>
<p>Default Array: <span v-text="defaultArray" /> (<span v-text="typeof defaultArray" />)</p>
<p>Multiple Types: <span v-text="multipleTypes" /> (<span v-text="typeof multipleTypes" />)</p>
<p>Data From Method: <span v-text="dataFromMethod" /> (<span v-text="typeof dataFromMethod" />)</p>
<p>Renamed: <span v-text="renamed" /> (<span v-text="typeof renamed" />)</p>
<p>JSON: <span v-text="json" /> (<span v-text="typeof json" />)</p>
</div>