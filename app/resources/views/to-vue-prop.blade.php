<x-layout>
    <x-to-vue-prop
        :int="1"
        :bool="false"
        :array="['foo', 'bar']"
        :object="(object) ['foo' => 'bar']"
    />
</x-layout>