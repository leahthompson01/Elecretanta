import { forwardRef, useEffect, useImperativeHandle, useRef } from "react";

export default forwardRef(function TextInput(
    { type = "text", className = "", isFocused = false, ...props },
    ref
) {
    const localRef = useRef(null);

    useImperativeHandle(ref, () => ({
        focus: () => localRef.current?.focus(),
    }));

    useEffect(() => {
        if (isFocused) {
            localRef.current?.focus();
        }
    }, [isFocused]);

    return (
        <input
            {...props}
            type={type}
            className={
                "text-[#B88914] rounded-[1000px] bg-[#F2E8CF] border-black border-solid	shadow-sm focus:border-indigo-500 focus:ring-indigo-500" +
                className
            }
            ref={localRef}
        />
    );
});
