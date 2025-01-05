import {Head, usePage} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/GuestLayout.jsx";
import {hobbies} from "../../lib/Hobbies.js";

export default function Hobbies(){
    const user = usePage().props.auth.user;
    return(
        <AuthenticatedLayout
            header={
                <h2 className="text-xl md:text-4xl font-semibold leading-tight text-gray-800">
                    {user.name}'s Hobbies
                </h2>
            }
        >
            <Head title="Hobbies" />
            <form className={'flex flex-col justify-center items-center gap-8'}>
            <div className={'mt-4 max-h-52 sm:max-h-full overflow-y-auto flex gap-4 flex-wrap'}>
                {hobbies.map(el =>
                <div className={'p-2 md:p-4 rounded-lg bg-[#B88914] text-[#F2E8CF] font-sans font-bold'}>
                    {el}
                </div>
                )}
            </div>
                <button className={'px-12 py-2 rounded-md bg-red-700/80 text-white md:text-2xl font-baskerville font-bold'}>Submit</button>
            </form>
        </AuthenticatedLayout>
    )
}
