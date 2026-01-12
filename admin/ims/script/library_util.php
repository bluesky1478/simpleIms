<script type="text/javascript">
    /**
     * 비트 마스크 팩토리
     * @param cfg
     * @returns {{}}
     */
    function makeBitmaskComputed(cfg) {
        const key = cfg.key;                 // prefix (ex: 'designTeam')
        const srcKey = cfg.srcKey;           // 옵션 소스 객체 필드명
        const valuePath = cfg.valuePath;     // 저장값 경로 (ex: 'mainData.designTeamInfo')
        const emptyText = cfg.emptyText || '-';
        const includeZero = cfg.includeZero === true;

        if (!key || !srcKey || !valuePath) throw new Error('makeBitmaskComputed: key/srcKey/valuePath required');

        const listName = `${key}OptionList`;
        const selectedName = `${key}Selected`;
        const textName = `${key}Text`;

        const getByPath = (ctx, path) =>
            path.split('.').reduce((o, k) => (o ? o[k] : undefined), ctx);

        const setByPath = (ctx, path, value) => {
            const parts = path.split('.');
            const last = parts.pop();
            const target = parts.reduce((o, k) => (o ? o[k] : undefined), ctx);
            if (!target) return;
            target[last] = value;
        };

        const computed = {};

        computed[listName] = function () {
            const src = this[srcKey] || {};
            return Object.keys(src)
                .map(k => Number(k))
                .filter(v => (includeZero ? v >= 0 : v > 0))
                .sort((a, b) => a - b)
                .map(v => ({ value: v, label: src[String(v)] }));
        };

        computed[selectedName] = {
            get() {
                const mask = Number(getByPath(this, valuePath) || 0);
                const list = this[listName] || [];
                return list.map(o => o.value).filter(v => (mask & v) === v);
            },
            set(selectedArr) {
                const mask = (selectedArr || []).reduce((acc, v) => acc | Number(v), 0);
                setByPath(this, valuePath, mask);
            }
        };

        computed[textName] = function () {
            const selected = this[selectedName] || [];
            if (!selected.length) return emptyText;

            const src = this[srcKey] || {};
            return selected.map(v => src[String(v)]).filter(Boolean).join(', ');
        };

        return computed;
    }

</script>