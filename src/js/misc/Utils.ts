class Utils {
    /**
     * @param {number} min
     * @param {number} max
     * @returns {number}
     */
    public static mtRand(min: number, max: number): number {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    /**
     * @param {number[]} input
     * @returns {number[]}
     */
    public static shuffle(input: number[]): number[] {
        let m = input.length;
        // While there remain elements to shuffle…
        while (m) {
            // Pick a remaining element…
            const i = Math.floor(Math.random() * m--);
            // And swap it with the current element.
            const t = input[m];
            input[m] = input[i];
            input[i] = t;
        }
        return input;
    }
}

export default Utils;