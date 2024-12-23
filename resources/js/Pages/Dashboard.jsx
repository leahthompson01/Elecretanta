import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import SecondaryButton from "@/Components/SecondaryButton";
import gear from "../../assets/icons/gear.svg";
import gift from "../../assets/icons/gift.svg";
import heart from "../../assets/icons/heart.svg";
import lightbulb from "../../assets/icons/lightbulb.svg";

export default function Dashboard() {
    const dashboardItems = [
        {
            image: gift,
            title: "gift exchanges",
            link: "/",
        },
        {
            image: heart,
            title: "hobbies",
            link: "/",
        },
        {
            image: lightbulb,
            title: "ideas",
            link: "/",
        },
        {
            image: gear,
            title: "settings",
            link: "/",
        },
    ];
    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            {/* <SecondaryButton className="ms-4">Test</SecondaryButton>
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            You're logged in!
                        </div>
                    </div>
                </div>
            </div> */}
            <h2 className="leading-tight text-black font-bold text-5xl tracking-[-.005em] font-baskerville">
                Hello, RICHARD
            </h2>
            <ul className="grid grid-cols-[repeat(auto-fit,minmax(15.625rem,1fr))] gap-6 justify-between py-12 ">
                {dashboardItems.map((item) => {
                    const { image, title, link } = item;
                    return (
                        <li className="border border-solid border-[#B88914] rounded-2xl flex-1 bg-[#FAF7EE] px-9 py-8">
                            <div>
                                <img src={image} alt="" className="mx-auto" />
                            </div>
                            <h2 className="capitalize font-bold text-2xl text-center ">
                                {title}
                            </h2>
                        </li>
                    );
                })}
            </ul>
            <SecondaryButton className="w-full font-baskerville justify-center py-4 text-2xl max-w-max px-8 font-bold bg-[#BC4749]">
                Make your list
            </SecondaryButton>
        </AuthenticatedLayout>
    );
}
