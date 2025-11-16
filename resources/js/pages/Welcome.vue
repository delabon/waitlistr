<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
</script>

<template>
    <Head title="Welcome"/>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-2 font-bold text-xl">Join the waitlist!</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.
                    </p>

                    <Form
                        action="/waitlist-signups"
                        method="post"
                        #default="{
                            errors,
                            processing,
                            wasSuccessful,
                        }"
                        class="flex flex-col gap-4 mt-6"
                        resetOnSuccess
                    >
                        <div class="flex flex-wrap gap-3 w-full">
                            <label class="inline-flex flex-col gap-2 flex-1">
                                <strong>First Name</strong>
                                <Input type="text" placeholder="John" name="firstName"/>
                                <div v-if="errors.firstName" class="text-red-500">
                                    {{ errors.firstName }}
                                </div>
                            </label>

                            <label class="inline-flex flex-col gap-2 flex-1">
                                <strong>Last Name</strong>
                                <Input type="text" placeholder="Doe" name="lastName"/>
                                <div v-if="errors.lastName" class="text-red-500">
                                    {{ errors.lastName }}
                                </div>
                            </label>
                        </div>

                        <label class="flex flex-col gap-2">
                            <strong>Email</strong>
                            <Input type="email" placeholder="john@example.com" name="email"/>
                            <div v-if="errors.email" class="text-red-500">
                                {{ errors.email }}
                            </div>
                        </label>

                        <Button class="cursor-pointer" type="submit">
                            {{ processing ? "Joining..." : "Signup Now - We're Launching Soon!" }}
                        </Button>

                        <div v-if="wasSuccessful" class="w-full text-center text-green-500">
                            You've joined the list successfully! <br/>
                            You should receive a welcome email shortly.
                        </div>
                    </Form>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
