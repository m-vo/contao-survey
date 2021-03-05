import builtins from 'rollup-plugin-node-builtins';
import commonjs from '@rollup/plugin-commonjs';
import resolve from '@rollup/plugin-node-resolve';
import scss from 'rollup-plugin-scss'
import typescript from '@rollup/plugin-typescript';
import {terser} from "rollup-plugin-terser";

export default {
    input: `layout/backend/survey.ts`,
    output: {
        file: `src/Resources/public/survey_backend.js`,
        format: 'iife',
        intro: 'const global = window;'
    },
    plugins: [
        builtins(),
        resolve(),
        commonjs(),
        typescript(),
        terser(),
        scss(),
    ]
};