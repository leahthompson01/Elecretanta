import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("register"), {
            onFinish: () => reset("password", "password_confirmation"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />
            <div className="w-11/12 md:w-1/2 mx-auto">
                <h1 className="font-baskerville text-4xl mb-8">Sign up</h1>
            </div>
            <form onSubmit={submit}>
                <div>
                    <InputLabel
                        htmlFor="name"
                        value="Name"
                        className="text-4xl text-center block w-full font-baskerville"
                    />

                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-11/12 md:w-1/2 mx-auto bg-[#F2E8CF] text-center font-bold text-[#B88914]"
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData("name", e.target.value)}
                        required
                    />

                    <InputError
                        message={errors.name}
                        className="mt-2 text-center" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="email"
                        value="Email"
                        className="text-4xl text-center block w-full font-baskerville"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-11/12 md:w-1/2 mx-auto bg-[#F2E8CF] text-center font-bold text-[#B88914]"
                        autoComplete="username"
                        onChange={(e) => setData("email", e.target.value)}
                        required
                    />

                    <InputError
                        message={errors.email}
                        className="mt-2 text-center" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="password"
                        value="Password"
                        className="text-4xl text-center block w-full font-baskerville"
                    />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-11/12 md:w-1/2 mx-auto bg-[#F2E8CF] text-center font-bold text-[#B88914]"
                        autoComplete="new-password"
                        onChange={(e) => setData("password", e.target.value)}
                        required
                    />

                    <InputError
                        message={errors.password}
                        className="mt-2 text-center" />
                </div>

                {/* <div className="mt-4">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                        className="text-4xl text-center block w-full font-baskerville"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-11/12 md:w-1/2 mx-auto bg-[#F2E8CF] text-center font-bold text-[#B88914]"
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData("password_confirmation", e.target.value)
                        }
                        required
                    />

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2 text-center"
                    />
                </div> */}
                <div className="w-11/12 md:w-1/2 mx-auto mt-10">
                    <div className="w-full md:w-full mx-auto">
                        <div className="flex justify-center md:justify-start">
                            <PrimaryButton
                                className="font-baskerville normal-case text-xl tracking-normal w-full md:w-1/3 flex justify-center items-center"
                                disabled={processing}
                            >
                                Sign up
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
                <div className="flex justify-center w-full mt-6">
                    <Link
                        href={route("login")}
                        className="text-xl md:text-2xl text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already have an account?{" "}
                        <span className="underline">Login here.</span>
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}
